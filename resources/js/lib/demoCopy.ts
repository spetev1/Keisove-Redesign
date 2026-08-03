/**
 * Everything on the homepage that the live store does not publish anywhere.
 *
 * The approved design leads on figures - twelve thousand products, forty
 * brands, a 4.8 rating from 2 100 customers, three named reviews - that exist
 * in the design document and nowhere else. They are built as designed, but they
 * are collected here rather than spread through the components, so confirming
 * or replacing them before the pitch is one file to open and no template to
 * hunt through.
 *
 * Anything a real figure exists for is NOT in this file. Department product
 * counts, the perfume from-price, which handsets are stocked and which products
 * are discounted all come from Postgres.
 *
 * In the production store these become editable content.
 */

/** Scrolls past the top of every page. */
export const ANNOUNCEMENTS: string[] = [
    'Доставка до 24 часа · 6.95 лв.',
    'Безплатна доставка над 60 лв.',
    'Промоция -50% на избрани калъфи',
    'Над 12 000 продукта в наличност',
];

export const HERO_COPY = {
    badge: '-50% на избрани модели',
    headline: 'Калъф за всеки телефон.',
    headlineSecondLine: 'Днес поръчан, утре доставен.',
    body: 'Над 12 000 аксесоара за 40+ марки - калъфи, протектори, оригинални зарядни и батерии. Наличност в реално време и доставка за 6.95 лв.',
    /** Neither claim is published; both are the design's. */
    rating: '4.8 от 2 100+ клиенти',
    returns: '14 дни право на връщане',
    promotionsCta: 'Виж промоциите',
    browseCta: 'Намери по модел',
};

export const COUNTDOWN_COPY = {
    eyebrow: 'Промоцията изтича',
    headline: '-50% на всички калъфи и протектори',
    body: 'Цените в количката вече са с намалението. Валидно до изчерпване на количествата.',
    /** Singular is never needed: a unit only shows while it is plural or zero. */
    units: {
        days: 'дни',
        hours: 'часа',
        minutes: 'мин',
        seconds: 'сек',
    },
};

/**
 * The four promises the design puts under the hero. The glyph stands in for an
 * icon: each is a figure rather than a picture, which is what the design draws.
 */
export type ServicePromise = {
    glyph: string;
    label: string;
    detail: string;
    /** Alternates so the row reads as a set rather than four of the same. */
    tone: 'accent' | 'ink';
};

export const SERVICE_PROMISES: ServicePromise[] = [
    {
        glyph: 'лв',
        label: 'Доставка 6.95 лв.',
        detail: 'Безплатно над 60 лв.',
        tone: 'accent',
    },
    {
        glyph: '24ч',
        label: 'Изпращаме до 24ч.',
        detail: 'Поръчки до 16:00',
        tone: 'ink',
    },
    {
        glyph: '✓',
        label: 'Оригинални части',
        detail: 'Гаранция 12 месеца',
        tone: 'accent',
    },
    {
        glyph: '14',
        label: 'Връщане 14 дни',
        detail: 'Без обяснения',
        tone: 'ink',
    },
];

export const CATEGORIES_COPY = {
    heading: 'Пазарувай по категория',
    subheading: 'Най-търсеното тази седмица',
    /**
     * The design writes a figure into this link. The taxonomy now has a real one
     * - departments plus their subcategories - so the page counts rather than
     * claims, and only the wording lives here.
     */
    allCategoriesPrefix: 'Всички',
    allCategoriesSuffix: 'категории',
};

export const NEW_PRODUCTS_COPY = {
    heading: 'Нови продукти',
    /*
     * The design names four handsets here, because the grid it draws is all
     * cases. This one is fed the newest products from every department, so it
     * says so instead - the pills beside the heading are what narrow it to one.
     */
    subheading: 'Последно добавени във всички отдели',
    /** The demo seeds a slice of the range, so this total is the live store's. */
    allProductsLabel: 'Виж всички 340 нови продукта',
    allFilterLabel: 'Всички',
    stockLabel: 'В наличност',
    buyLabel: 'Купи',
};

export const BRANDS_COPY = {
    heading: 'Търси по марка',
    /** The demo stocks five handset families; 40+ is the live store's figure. */
    supportedCount: '40+ поддържани марки',
    allLabel: 'Всички',
};

export type Testimonial = {
    quote: string;
    author: string;
    city: string;
};

/**
 * Written for the design document. Real reviews would come from whatever the
 * store collects them in, and these should not survive the pitch as-is.
 */
export const TESTIMONIALS: Testimonial[] = [
    {
        quote: 'Поръчах в 14:00, на другия ден куриерът беше пред вратата. Калъфът пасва идеално.',
        author: 'Мартин Г.',
        city: 'Пловдив',
    },
    {
        quote: 'Намерих протектор за стар модел, който никъде другаде го нямаше. Оригинално качество.',
        author: 'Даниела П.',
        city: 'София',
    },
    {
        quote: 'Обслужване по телефона на ниво - помогнаха ми да избера точния модел за Moto G87.',
        author: 'Иван С.',
        city: 'Варна',
    },
];

/**
 * Only the labels. Both cards name a real handset family and a real department,
 * and the bestseller card's price is the department's genuine lowest, so those
 * come from the controller rather than from here.
 */
export const SPOTLIGHT_COPY = {
    newArrivals: {
        eyebrow: 'Ново',
        titlePrefix: 'Аксесоари за',
        cta: 'Разгледай',
    },
    bestseller: {
        eyebrow: 'Бестселър',
        fromLabel: 'от',
        cta: 'Виж колекцията',
    },
};

export const NEWSLETTER_COPY = {
    eyebrow: 'Само за нови клиенти',
    headline: 'Вземи -10% на първата поръчка',
    body: 'Абонирай се и получавай промоциите преди всички. Без спам.',
    sent: 'Готово! Кодът е на път към имейла ти.',
    cta: 'Абонирай се',
    sentCta: 'Изпратено ✓',
    badge: '-10%',
};

export const FOOTER_COPY = {
    /** The founding year is the design's; it is not published on the store. */
    blurb: 'Онлайн магазин за аксесоари за мобилни телефони от 2011 г.',
    paymentHeading: 'Плащане',
    paymentMethods: ['Наложен платеж', 'Карта', 'Банков превод'],
    rights: 'Всички права запазени',
    vatNote: 'Цените са в лв. с ДДС',
};
