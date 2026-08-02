<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import type { ComponentPublicInstance } from 'vue';

/**
 * A button whose outline is drawn in WebGL rather than in CSS: a light travels
 * around the rounded rectangle and the edges facing it catch a specular
 * streak, which is the part a plain border cannot do.
 *
 * Ported from the React original at reactbits.dev. The shader is carried over
 * unchanged; the renderer it shipped with (ogl) is not a dependency of this
 * project, and everything it was doing here - one full-screen triangle and one
 * program - is short enough to set up by hand.
 */

type Size = 'sm' | 'md' | 'lg';

type Props = {
    size?: Size;
    /** Corner radius in CSS pixels, clamped to half the shorter side. */
    radius?: number;
    /** Fill behind the label, invisible until `tintOpacity` is raised. */
    tint?: string;
    tintOpacity?: number;
    blur?: number;
    textColor?: string;
    /** The streak itself. */
    lineColor?: string;
    /** The dull stroke hugging the edge that gives the outline its weight. */
    baseColor?: string;
    intensity?: number;
    /** Half-width of the lit arc in degrees, and how far it feathers out. */
    shineSize?: number;
    shineFade?: number;
    thickness?: number;
    /** Radians per second the light drifts when nothing is steering it. */
    speed?: number;
    followMouse?: boolean;
    /** How near the pointer must come, in pixels, before the streak lights. */
    proximity?: number;
    /** Keep the streak lit and moving whether or not the pointer is near. */
    autoAnimate?: boolean;
    /** Given one, the button renders as a link rather than as a button. */
    href?: string;
    disabled?: boolean;
    type?: 'button' | 'submit' | 'reset';
};

const props = withDefaults(defineProps<Props>(), {
    size: 'lg',
    radius: 18,
    tint: '#ffffff',
    tintOpacity: 0,
    blur: 0,
    textColor: '#f5f5f5',
    lineColor: '#ffffff',
    baseColor: '#525252',
    intensity: 1,
    shineSize: 10,
    shineFade: 40,
    thickness: 1,
    speed: 0.35,
    followMouse: true,
    proximity: 250,
    autoAnimate: false,
    disabled: false,
    type: 'button',
});

/**
 * The canvas overhangs the button on every side, so the streak can bloom past
 * the edge instead of being clipped by it.
 */
const PAD = 20;

const SIZE_CLASSES: Record<Size, string> = {
    sm: 'px-[22px] py-[10px] text-[0.85rem]',
    md: 'px-[30px] py-[14px] text-[1rem]',
    lg: 'px-10 py-[18px] text-[1.15rem]',
};

const VERTEX_SHADER = `#version 300 es
in vec2 position;
void main() {
  gl_Position = vec4(position, 0.0, 1.0);
}
`;

const FRAGMENT_SHADER = `#version 300 es
precision highp float;

uniform vec2 uCenter;
uniform vec2 uHalfSize;
uniform float uRadius;
uniform float uAngle;
uniform float uPx;
uniform vec3 uLineColor;
uniform vec3 uBaseColor;
uniform float uIntensity;
uniform float uShineSize;
uniform float uShineFade;
uniform float uThickness;
uniform float uBaseWidth;

out vec4 fragColor;

float sdRoundedRect(vec2 p, vec2 b, float r) {
  vec2 q = abs(p) - b + r;
  return length(max(q, 0.0)) + min(max(q.x, q.y), 0.0) - r;
}

float shapeSDF(vec2 p) { return sdRoundedRect(p, uHalfSize, uRadius); }

float gaussianLine(float d, float sigma) {
  float x = d / (sigma + 1e-6);
  float k = mix(1.0, 1.6, smoothstep(0.0, 1.5, x));
  return exp(-k * x * x);
}

void main() {
  vec2 p = gl_FragCoord.xy - uCenter;
  float d = shapeSDF(p);
  vec2 L = vec2(cos(uAngle), sin(uAngle));

  // Dark base stroke hugging the edge for a sense of thickness
  float base = (1.0 - smoothstep(0.0, uBaseWidth, abs(d))) * 0.45;

  // Symmetric specular: the edges facing toward/away from the light both
  // catch a streak. The angular window (size + fade) is measured with an
  // elliptical normal so it varies continuously along straight edges.
  vec2 nEll = normalize(p / (uHalfSize * uHalfSize) + 1e-6);
  float phi = acos(clamp(abs(dot(nEll, L)), 0.0, 1.0));
  float rim = 1.0 - smoothstep(uShineSize - uShineFade, uShineSize + uShineFade + 1e-4, phi);
  float line = gaussianLine(d, uThickness);
  float edgeClamp = 1.0 - smoothstep(0.5 * uPx, 3.0 * uPx, abs(d));
  float hi = line * rim * edgeClamp * uIntensity;

  vec3 col = uBaseColor * base + uLineColor * hi;
  float a = clamp(base + hi, 0.0, 1.0);
  fragColor = vec4(col, a);
}
`;

/** Every uniform the fragment shader declares, looked up once at startup. */
type Uniforms = Record<string, WebGLUniformLocation | null>;

const rootRef = ref<HTMLElement | ComponentPublicInstance | null>(null);
const canvasRef = ref<HTMLCanvasElement | null>(null);

/**
 * `#` marks a destination the demo does not build yet; routing that through
 * Inertia would fire a visit at the current page.
 */
const rootComponent = computed(() => {
    if (!props.href) {
        return 'button';
    }

    return props.href === '#' ? 'a' : Link;
});

/** Rendered as a link, the root is a component, so the element is one step in. */
function rootElement(): HTMLElement | null {
    const root = rootRef.value;

    if (!root) {
        return null;
    }

    return root instanceof HTMLElement ? root : (root.$el as HTMLElement);
}

/**
 * False when WebGL2 is unavailable, in which case the outline falls back to a
 * plain border - without one the button would be bare text.
 */
const isShaderDrawing = ref(false);

let gl: WebGL2RenderingContext | null = null;
let program: WebGLProgram | null = null;
let uniforms: Uniforms = {};
let resizeObserver: ResizeObserver | null = null;
let frameId = 0;
let pixelRatio = 1;

let cssSize = { width: 1, height: 1 };
let center: [number, number] = [0, 0];
let halfSize: [number, number] = [1, 1];

let angle = 2.4;
let idleAngle = 2.4;
let brightness = 0;
let pointerAngle: number | null = null;
let proximityFactor = 0;
let lastFrameAt = 0;

/** Resolved on change rather than per frame, since resolving reads the DOM. */
let lineRgb: [number, number, number] = [1, 1, 1];
let baseRgb: [number, number, number] = [0.32, 0.32, 0.32];

/**
 * Colours are taken as CSS rather than as literals, so a design token like
 * `var(--brand-highlight)` can be handed straight to the button and the brand
 * stays in one stylesheet. The browser does the resolving: a hidden probe
 * inside the button inherits exactly the custom properties the button sees.
 *
 * @return Channels in 0..1, falling back to white on anything the browser
 *   cannot resolve rather than pushing NaN into a uniform and blanking the
 *   outline.
 */
function resolveColor(color: string): [number, number, number] {
    const host = rootElement();

    if (!host) {
        return [1, 1, 1];
    }

    const probe = document.createElement('span');

    probe.style.display = 'none';
    // Assigning an unresolvable colour is a no-op, so this white stands as the
    // fallback rather than the button's own inherited text colour.
    probe.style.color = 'rgb(255, 255, 255)';
    probe.style.color = color;
    host.appendChild(probe);

    const computed = window.getComputedStyle(probe).color;

    probe.remove();

    const channels = computed.startsWith('rgb')
        ? computed.match(/[\d.]+/g)
        : null;

    if (!channels || channels.length < 3) {
        return [1, 1, 1];
    }

    return [
        Number(channels[0]) / 255,
        Number(channels[1]) / 255,
        Number(channels[2]) / 255,
    ];
}

function refreshColors(): void {
    lineRgb = resolveColor(props.lineColor);
    baseRgb = resolveColor(props.baseColor);

    // Recoloured while the loop is parked - under reduced motion, say - the
    // outline would otherwise keep its old colour until something redrew it.
    if (!frameId) {
        render();
    }
}

function compileShader(
    context: WebGL2RenderingContext,
    type: number,
    source: string,
): WebGLShader | null {
    const shader = context.createShader(type);

    if (!shader) {
        return null;
    }

    context.shaderSource(shader, source);
    context.compileShader(shader);

    if (!context.getShaderParameter(shader, context.COMPILE_STATUS)) {
        context.deleteShader(shader);

        return null;
    }

    return shader;
}

function createProgram(context: WebGL2RenderingContext): WebGLProgram | null {
    const vertex = compileShader(context, context.VERTEX_SHADER, VERTEX_SHADER);
    const fragment = compileShader(
        context,
        context.FRAGMENT_SHADER,
        FRAGMENT_SHADER,
    );

    if (!vertex || !fragment) {
        return null;
    }

    const created = context.createProgram();

    if (!created) {
        return null;
    }

    context.attachShader(created, vertex);
    context.attachShader(created, fragment);
    context.linkProgram(created);

    // The shaders are only ever used by this one program, so they can go as
    // soon as it is linked.
    context.deleteShader(vertex);
    context.deleteShader(fragment);

    if (!context.getProgramParameter(created, context.LINK_STATUS)) {
        context.deleteProgram(created);

        return null;
    }

    return created;
}

/**
 * One triangle large enough to cover the clip space, which is cheaper than two
 * and leaves the fragment shader to decide what is actually drawn.
 */
function createTriangle(
    context: WebGL2RenderingContext,
    linked: WebGLProgram,
): void {
    context.bindBuffer(context.ARRAY_BUFFER, context.createBuffer());
    context.bufferData(
        context.ARRAY_BUFFER,
        new Float32Array([-1, -1, 3, -1, -1, 3]),
        context.STATIC_DRAW,
    );

    const position = context.getAttribLocation(linked, 'position');

    context.enableVertexAttribArray(position);
    context.vertexAttribPointer(position, 2, context.FLOAT, false, 0, 0);
}

function readUniforms(
    context: WebGL2RenderingContext,
    linked: WebGLProgram,
): Uniforms {
    const names = [
        'uCenter',
        'uHalfSize',
        'uRadius',
        'uAngle',
        'uPx',
        'uLineColor',
        'uBaseColor',
        'uIntensity',
        'uShineSize',
        'uShineFade',
        'uThickness',
        'uBaseWidth',
    ];

    return Object.fromEntries(
        names.map((name) => [name, context.getUniformLocation(linked, name)]),
    );
}

/**
 * Fractional size and an explicit centre keep the shape pinned to the exact
 * CSS border, rather than drifting up to a pixel away as `offsetWidth`
 * rounding would.
 */
function resize(): void {
    const root = rootElement();
    const canvas = canvasRef.value;

    if (!gl || !root || !canvas) {
        return;
    }

    const rect = root.getBoundingClientRect();

    cssSize = { width: rect.width, height: rect.height };
    canvas.width = Math.max(1, Math.round((rect.width + PAD * 2) * pixelRatio));
    canvas.height = Math.max(
        1,
        Math.round((rect.height + PAD * 2) * pixelRatio),
    );

    gl.viewport(0, 0, canvas.width, canvas.height);

    center = [
        (PAD + rect.width / 2) * pixelRatio,
        (PAD + rect.height / 2) * pixelRatio,
    ];
    halfSize = [(rect.width / 2) * pixelRatio, (rect.height / 2) * pixelRatio];
}

/**
 * The light steers toward the pointer anywhere on the page, not just over the
 * button, so a cursor on its way past already bends the streak.
 */
function handlePointerMove(event: PointerEvent): void {
    const root = rootElement();

    if (!root) {
        return;
    }

    const rect = root.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    const distanceX = Math.max(
        rect.left - event.clientX,
        0,
        event.clientX - rect.right,
    );
    const distanceY = Math.max(
        rect.top - event.clientY,
        0,
        event.clientY - rect.bottom,
    );
    const distance = Math.hypot(distanceX, distanceY);

    if (distance === 0) {
        // Over the button itself the light settles on the diagonal, framing the
        // corners, and sways only gently with the cursor inside it.
        const offsetX = (event.clientX - centerX) / (rect.width / 2);
        const offsetY = (centerY - event.clientY) / (rect.height / 2);

        pointerAngle =
            Math.atan2(2 / rect.height, -2 / rect.width) +
            offsetX * 0.3 +
            offsetY * 0.15;
    } else {
        pointerAngle = Math.atan2(
            centerY - event.clientY,
            event.clientX - centerX,
        );
    }

    const nearness = Math.max(0, 1 - distance / Math.max(props.proximity, 1));

    proximityFactor = nearness * nearness * (3 - 2 * nearness);
}

function render(): void {
    if (!gl || !program) {
        return;
    }

    const radius =
        Math.min(props.radius, Math.min(cssSize.width, cssSize.height) / 2) *
        pixelRatio;

    gl.uniform2f(uniforms.uCenter, center[0], center[1]);
    gl.uniform2f(uniforms.uHalfSize, halfSize[0], halfSize[1]);
    gl.uniform1f(uniforms.uRadius, radius);
    gl.uniform1f(uniforms.uAngle, angle);
    gl.uniform1f(uniforms.uPx, pixelRatio);
    gl.uniform3f(uniforms.uLineColor, lineRgb[0], lineRgb[1], lineRgb[2]);
    gl.uniform3f(uniforms.uBaseColor, baseRgb[0], baseRgb[1], baseRgb[2]);
    gl.uniform1f(uniforms.uIntensity, props.intensity * brightness);
    gl.uniform1f(uniforms.uShineSize, (props.shineSize * Math.PI) / 180);
    gl.uniform1f(uniforms.uShineFade, (props.shineFade * Math.PI) / 180);
    gl.uniform1f(uniforms.uThickness, props.thickness * pixelRatio);
    gl.uniform1f(uniforms.uBaseWidth, pixelRatio);

    gl.clear(gl.COLOR_BUFFER_BIT);
    gl.drawArrays(gl.TRIANGLES, 0, 3);
}

function step(now: number): void {
    frameId = requestAnimationFrame(step);

    // Capped so a backgrounded tab does not resume with one enormous step.
    const delta = Math.min((now - lastFrameAt) / 1000, 0.05);

    lastFrameAt = now;
    idleAngle += props.speed * delta;

    const isSteered =
        props.followMouse &&
        pointerAngle !== null &&
        (!props.autoAnimate || proximityFactor > 0);
    const target =
        isSteered && pointerAngle !== null ? pointerAngle : idleAngle;
    // Taken the short way round, so the light never unwinds the long way to
    // reach an angle just across the wrap point.
    const difference =
        ((target - angle + Math.PI * 3) % (Math.PI * 2)) - Math.PI;

    angle += difference * (1 - Math.exp(-delta * 7));

    const brightnessTarget = props.autoAnimate ? 1 : proximityFactor;

    brightness += (brightnessTarget - brightness) * (1 - Math.exp(-delta * 8));

    render();
}

watch(() => [props.lineColor, props.baseColor], refreshColors);

function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

onMounted(() => {
    const root = rootElement();
    const canvas = canvasRef.value;

    if (!root || !canvas) {
        return;
    }

    pixelRatio = window.devicePixelRatio || 1;
    gl = canvas.getContext('webgl2', {
        alpha: true,
        premultipliedAlpha: true,
        antialias: true,
    });

    if (!gl) {
        return;
    }

    program = createProgram(gl);

    if (!program) {
        gl = null;

        return;
    }

    gl.useProgram(program);
    createTriangle(gl, program);
    uniforms = readUniforms(gl, program);

    gl.clearColor(0, 0, 0, 0);
    gl.enable(gl.BLEND);
    gl.blendFunc(gl.ONE, gl.ONE_MINUS_SRC_ALPHA);

    isShaderDrawing.value = true;

    resizeObserver = new ResizeObserver(() => {
        resize();

        // A resize while the loop is parked would otherwise leave the outline
        // drawn at the old size until something else redraws it.
        if (!frameId) {
            render();
        }
    });
    resizeObserver.observe(root);
    resize();
    refreshColors();

    if (prefersReducedMotion()) {
        // One still frame: the outline is there, the light just does not travel.
        brightness = props.autoAnimate ? 1 : 0;
        render();

        return;
    }

    if (props.followMouse || !props.autoAnimate) {
        // Nothing reads the pointer when the light is on a timer and ignoring
        // it, so in that case the listener is not worth its own work.
        window.addEventListener('pointermove', handlePointerMove);
    }

    lastFrameAt = performance.now();
    frameId = requestAnimationFrame(step);
});

onBeforeUnmount(() => {
    cancelAnimationFrame(frameId);
    frameId = 0;
    resizeObserver?.disconnect();
    window.removeEventListener('pointermove', handlePointerMove);

    if (program) {
        gl?.deleteProgram(program);
        program = null;
    }

    gl?.getExtension('WEBGL_lose_context')?.loseContext();
    gl = null;
});
</script>

<template>
    <component
        :is="rootComponent"
        ref="rootRef"
        :href="href"
        :type="href ? undefined : type"
        :disabled="href ? undefined : disabled"
        :class="[
            'relative m-0 inline-flex cursor-pointer items-center justify-center leading-none font-medium tracking-[0.01em] shadow-[inset_0_1px_0_rgba(255,255,255,0.04),0_8px_24px_rgba(0,0,0,0.25)] transition-transform duration-150 outline-none focus-visible:outline-2 focus-visible:outline-offset-[3px] active:scale-[0.97] disabled:cursor-default disabled:opacity-55 disabled:active:scale-100 motion-reduce:transition-none',
            SIZE_CLASSES[size],
            isShaderDrawing ? 'border-none' : 'border border-solid',
        ]"
        :style="{
            borderRadius: `${radius}px`,
            borderColor: isShaderDrawing ? undefined : baseColor,
            color: textColor,
            background: `color-mix(in srgb, ${tint} ${tintOpacity * 100}%, transparent)`,
            backdropFilter: blur > 0 ? `blur(${blur}px)` : undefined,
        }"
    >
        <span
            class="pointer-events-none absolute -inset-5 z-[1]"
            aria-hidden="true"
        >
            <canvas ref="canvasRef" class="block size-full" />
        </span>
        <span class="relative z-[2]"><slot /></span>
    </component>
</template>
