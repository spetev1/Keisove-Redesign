<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\CaseType;
use App\Support\DeviceFamily;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The demo catalogue - a slice of the live keisove.net range, with the
     * shop's own product photography.
     *
     * The shelves are the design document's categories. Cases are divided the
     * way the design divides them, by what the case is built from, so a silicone
     * back and a leather flip sit in different departments even though both are
     * cases for the same handset.
     *
     * Which handset a product fits stays a column rather than a category - see
     * the note on the categories migration - so one row in "Силиконови гърбове"
     * carrying `device_family` covers what the live store spreads over
     * twenty-three per-handset categories.
     */
    public function run(): void
    {
        $brands = Brand::pluck('id', 'slug');
        $categories = Category::get()->keyBy('slug');

        $catalogue = [
            'silikonovi-garbove' => $this->siliconeBacks(),
            'kozheni-kalafi' => $this->leatherCases(),
            'protektori' => $this->protectors(),
            'zaryadni-220v' => $this->mainsChargers(),
            'zaryadni-12v' => $this->carChargers(),
            'bluetooth-slushalki' => $this->wirelessHeadphones(),
            'power-bank' => $this->powerBanks(),
            'smart-chasovnitsi' => $this->smartWatches(),
            'aksesoari-za-chasovnitsi' => $this->watchAccessories(),
            'damski-parfyumi' => $this->womensPerfumes(),
            'mazhki-parfyumi' => $this->mensPerfumes(),
            'uniseks-parfyumi' => $this->unisexPerfumes(),
            'tvardi-garbove' => $this->hardBacks(),
            'usb-kabeli' => $this->cables(),
            'prehodnitsi-i-adapteri' => $this->adapters(),
            'stoyki-za-kola' => $this->carMounts(),
            'kalafi-za-tableti' => $this->tabletCases(),
            'universalni-kalafi' => $this->universalCases(),
            'handsfree' => $this->wiredHeadsets(),
            'tonkoloni' => $this->speakers(),
            'originalni-baterii' => $this->replacementBatteries(),
            'karti-pamet' => $this->memoryCards(),
            'koli' => $this->toyCars(),
            'zanimatelni' => $this->activityToys(),
            'kolektsionerski-figurki' => $this->collectibleFigures(),
        ];

        foreach ($catalogue as $categorySlug => $products) {
            foreach ($products as $sortOrder => $product) {
                $this->createProduct(
                    $categories[$categorySlug],
                    $product,
                    $brands,
                    $sortOrder,
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $product
     * @param  Collection<string, int>  $brands
     */
    protected function createProduct(
        Category $category,
        array $product,
        Collection $brands,
        int $sortOrder,
    ): void {
        $brandSlug = $product['brand'];
        unset($product['brand']);

        Product::updateOrCreate(
            ['slug' => $product['slug']],
            [
                ...$product,
                'category_id' => $category->id,
                'brand_id' => $brandSlug ? $brands->get($brandSlug) : null,
                'sort_order' => $sortOrder,
            ],
        );
    }

    /**
     * Силиконови гърбове.
     *
     * @return list<array<string, mixed>>
     */
    protected function siliconeBacks(): array
    {
        return [
            [
                'name' => 'Хибриден кейс Etteri Morning Fog',
                'slug' => 'keis-etteri-morning-fog-iphone-17-pro-max',
                'subtitle' => 'iPhone 17 Pro Max · черен',
                'brand' => 'etteri',
                'device_family' => DeviceFamily::IPhone,
                'case_type' => CaseType::Hybrid,
                'image_path' => 'images/products/keis-etteri-morning-fog-iphone-17-pro-max.jpg',
                'price_in_stotinki' => 3895,
                'compare_at_price_in_stotinki' => 7790,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Защитният калъф от серията Morning Fog придава на вашия iPhone 17 Pro Max елегантен вид и надеждна защита.\nИзработен от издръжлив TPU материал, този тънък калъф ефективно абсорбира ударите, като същевременно осигурява удобно и неплъзгащо се захващане.\nПовдигнатите ръбове около екрана и модула на камерата предпазват най-чувствителните части на вашето устройство от надраскване при контакт с повърхности.",
            ],
            [
                'name' => 'Crystal Case с MagSafe',
                'slug' => 'crystal-case-magsafe-galaxy-s26-ultra',
                'subtitle' => 'Samsung Galaxy S26 Ultra · прозрачен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => CaseType::Silicone,
                'image_path' => 'images/products/crystal-case-magsafe-galaxy-s26-ultra.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Crystal Case е калъф от една част, състоящ се от прозрачен поликарбонатен слой, който обгръща ръбовете и гърба на телефона и издръжлива рамка, изработена от мек термопластичен материал, който допълнително предпазва устройството от повреда.\nТова пасва идеално на телефона и не ограничава възможността за безжично зареждане.\nЛеко повдигнатата форма около камерата и екрана го предпазва от надраскване, като повдига телефона точно над равна повърхност.",
            ],
            [
                'name' => 'Crystal Case с MagSafe',
                'slug' => 'crystal-case-magsafe-galaxy-s26',
                'subtitle' => 'Samsung Galaxy S26 · прозрачен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => CaseType::Silicone,
                'image_path' => 'images/products/crystal-case-magsafe-galaxy-s26.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Crystal Case е калъф от една част, състоящ се от прозрачен поликарбонатен слой, който обгръща ръбовете и гърба на телефона и издръжлива рамка, изработена от мек термопластичен материал, който допълнително предпазва устройството от повреда.\nТова пасва идеално на телефона и не ограничава възможността за безжично зареждане.\nЛеко повдигнатата форма около камерата и екрана го предпазва от надраскване, като повдига телефона точно над равна повърхност.",
            ],
            [
                'name' => 'Силиконов гръб Spigen Hybrid',
                'slug' => 'spigen-hybrid-tpu-iphone-15',
                'subtitle' => 'iPhone 15 (6.1) · черен',
                'brand' => 'spigen',
                'device_family' => DeviceFamily::IPhone,
                'case_type' => CaseType::Hybrid,
                'image_path' => 'images/products/spigen-hybrid-tpu-iphone-15.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "- Качествен хибриден кейс с двуслойна защита - външна поликарбонатова задна част и вътрешна TPU, като по този начин предоставя висока степен на защита\n- Материал: TPU (термополиуретан) и поликарбонат\n- TPU - този материал разполага с много удобства като продължителност на живота, малко механично триене, устойчивост на атмосферни влияния, гъвкавост в широк температурен диапазон и изключителна якост на сгъване и скъсване",
            ],
            [
                'name' => 'Силиконов гръб Spigen Hybrid',
                'slug' => 'spigen-hybrid-tpu-galaxy-s23',
                'subtitle' => 'Samsung Galaxy S23 · черен',
                'brand' => 'spigen',
                'device_family' => DeviceFamily::Samsung,
                'case_type' => CaseType::Hybrid,
                'image_path' => 'images/products/spigen-hybrid-tpu-galaxy-s23.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "- Качествен хибриден кейс с двуслойна защита - външна поликарбонатова задна част и вътрешна TPU, като по този начин предоставя висока степен на защита\n- Материал: TPU (термополиуретан) и поликарбонат\n- TPU - този материал разполага с много удобства като продължителност на живота, малко механично триене, устойчивост на атмосферни влияния, гъвкавост в широк температурен диапазон и изключителна якост на сгъване и скъсване",
            ],
            [
                'name' => 'Калъф Anti Shock TPU',
                'slug' => 'anti-shock-tpu-honor-x5c-plus',
                'subtitle' => 'Honor X5c Plus · прозрачен',
                'brand' => null,
                'device_family' => DeviceFamily::Honor,
                'case_type' => CaseType::Shockproof,
                'image_path' => 'images/products/anti-shock-tpu-honor-x5c-plus.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "- Калъфът за телефон Anti Shock е стилен начин да предпазите вашето устройство.\n- Серията Anti Shock се отличава с подсилени ъгли и увеличена дебелина до 1,5 мм. TPU материалът и дебелината му гарантират по-голяма защита на телефона от удари и драскотини.\n- Допълнителен грапав вътрешен слой предотвратява натрупването на влага и залепването на кейса към корпуса на смартфона.",
            ],
            [
                'name' => 'Калъф Nano TPU',
                'slug' => 'nano-tpu-honor-400-smart',
                'subtitle' => 'Honor 400 Smart / X7d · черен',
                'brand' => null,
                'device_family' => DeviceFamily::Honor,
                'case_type' => CaseType::Silicone,
                'image_path' => 'images/products/nano-tpu-honor-400-smart.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "- Елегантен силиконов гръб с матирано покритие.\n- Придава на телефона стилна визия, като едновременно с това го предпазва от евентуални удари, счупване или механични повреди.\n- Калъфът е функционален и удобен за ползване, тъй като разполага с всички входове, изходи и бутони на телефона.\n- Осигурява пълен достъп до екрана и камерата, като същевременно предпазва корпуса на мобилното устройство.\n- Матираният гръб е мек, гъвкав и устойчив на удари.",
            ],
        ];
    }

    /**
     * Кожени калъфи.
     *
     * @return list<array<string, mixed>>
     */
    protected function leatherCases(): array
    {
        return [
            [
                'name' => 'Кожен калъф Smart View Cover',
                'slug' => 'smart-view-cover-galaxy-a03',
                'subtitle' => 'Samsung Galaxy A03 · черен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/smart-view-cover-galaxy-a03.jpg',
                'price_in_stotinki' => 2945,
                'compare_at_price_in_stotinki' => 5890,
                'is_new' => false,
                'is_featured' => true,
                'description' => 'Елегантен и стилен дизайн. Цялостна защита за мобилното устройство. Предпазва от драскотини, наранявания и замърсявания. Калъфът може да бъде използван и като стойка. Лесен достъп до всички бутони и изходи на телефона.',
            ],
            [
                'name' => 'Кожен калъф / кобур / за колан за Samsung Galaxy S23 Ultra',
                'slug' => 'kozhen-kalaf-kobur-za-kolan-za-samsung-galaxy-s23-ultra',
                'subtitle' => 'черен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/kozhen-kalaf-kobur-za-kolan-za-samsung-galaxy-s23-ultra.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Кожен калъф / кобур / за колан за Samsung S23 Ultra - черен\n- Кожен калъф - кобур, лесно прикачащ се за колан и удобен за носене на кръста.\n- Затваря се чрез скрит магнит, благодарение на който предното капаче прилепва идеално към основата на калъфа\n- Осигурява безопасност и цялостна защита за мобилното устройство.",
            ],
            [
                'name' => 'Кожен калъф / кобур / за колан за Samsung Galaxy A34',
                'slug' => 'kozhen-kalaf-kobur-za-kolan-za-samsung-galaxy-a34-cheren',
                'subtitle' => 'черен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/kozhen-kalaf-kobur-za-kolan-za-samsung-galaxy-a34-cheren.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Кожен калъф / кобур / за колан за Samsung Galaxy A34 - черен\n- Кожен калъф - кобур, лесно прикачащ се за колан и удобен за носене на кръста.\n- Затваря се чрез скрит магнит, благодарение на който предното капаче прилепва идеално към основата на калъфа\n- Осигурява безопасност и цялостна защита за мобилното устройство.",
            ],
        ];
    }

    /**
     * Протектори.
     *
     * @return list<array<string, mixed>>
     */
    protected function protectors(): array
    {
        return [
            [
                'name' => '9H Magic Glass протектор',
                'slug' => '9h-magic-glass-galaxy-a17',
                'subtitle' => 'Samsung Galaxy A17',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/9h-magic-glass-galaxy-a17.png',
                'price_in_stotinki' => 995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "- Качествен стъклен протектор от закалено стъкло.\n- Лесно и бързо поставяне.\n- Защита от появата на драскотини по сензорния дисплей на устройството.\n- Защита при удар и изпускане.\n- Не влияе на качеството на дисплея и не намалява видимостта.\n- Съвместим модел: Samsung Galaxy A17\nМатериал: Закалено стъкло\nСъвместим модел: Samsung Galaxy A17\nСъвместима марка: Samsung\nТип: Прозрачен\nТип продукт: Протектор\nЦвят: Прозрачен",
            ],
            [
                'name' => '5D Full Cover стъклен протектор',
                'slug' => '5d-tempered-glass-galaxy-a17',
                'subtitle' => 'Samsung Galaxy A17 · черен кант',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/5d-tempered-glass-galaxy-a17.png',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => 4490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Модел: Full Glue Tempered Glass\nСъвместимост: Samsung A17\nЦвят: безцветен с черен кант\n- Най-висок клас стъклено покритие за дисплея на вашето мобилно устройство.\n- Материал- изработен от закалено стъкло с твърдост 9H, предпазващо от надраскване и други евентуални щети.\n- 99% Прозрачност- стъкления протектор е почти незабележим поради високата си прозрачност и не влияе на качеството на дисплея.",
            ],
            [
                'name' => 'Privacy 5D стъклен протектор',
                'slug' => 'privacy-5d-tempered-glass-galaxy-a17',
                'subtitle' => 'Samsung Galaxy A17 · черен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/privacy-5d-tempered-glass-galaxy-a17.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "ОП ИСАНИЕ\n- 5D Full Privacy стъклен протектор е висок клас стъклен протектор с незабележимо лепило по цялата повърхност на стъклото.\n- Този стъклен протектор ще покрие всеки милиметър от вашия дисплей, дори и по извитите части.\n- Създаден по изцяло нова технология от закалено стъкло с извитите краища, които покриват напълно дисплея на Вашия телефон.",
            ],
            [
                'name' => '9H Magic Glass протектор',
                'slug' => '9h-magic-glass-iphone-16-pro',
                'subtitle' => 'iPhone 16 Pro',
                'brand' => null,
                'device_family' => DeviceFamily::IPhone,
                'case_type' => null,
                'image_path' => 'images/products/9h-magic-glass-iphone-16-pro.png',
                'price_in_stotinki' => 945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "- Качествен стъклен протектор от закалено стъкло.\n- Лесно и бързо поставяне.\n- Защита от появата на драскотини по сензорния дисплей на устройството.\n- Защита при удар и изпускане.\n- Не влияе на качеството на дисплея и не намалява видимостта.\n- Съвместим модел: iPhone 16 Pro\nМатериал: Закалено стъкло\nСъвместим модел: iPhone 16 Pro\nСъвместима марка: iPhone\nТип: Прозрачен\nТип продукт: Протектор\nЦвят: Прозрачен",
            ],
            [
                'name' => 'Протектор за камера Etteri',
                'slug' => 'etteri-camera-protector-iphone-17-pro',
                'subtitle' => 'iPhone 17 Pro · черен',
                'brand' => 'etteri',
                'device_family' => DeviceFamily::IPhone,
                'case_type' => null,
                'image_path' => 'images/products/etteri-camera-protector-iphone-17-pro.jpg',
                'price_in_stotinki' => 2945,
                'compare_at_price_in_stotinki' => 5890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Защитете най-ценната част от вашия iPhone с безкомпромисно качество и стил.\nПротекторът за задна камера Etteri е проектиран специално за iPhone 17 Pro, като предлага пълно покритие на модула и индивидуална защита за всеки обектив.\nЗащо да изберете Etteri Full Camera Lens Guard?\nХибридна конструкция: Комбинация от закалено стъкло с твърдост 9H и лека, но здрава алуминиева рамка в наситен черен цвят.",
            ],
            [
                'name' => 'Протектор за камера Etteri',
                'slug' => 'etteri-camera-protector-iphone-17',
                'subtitle' => 'iPhone 17 · черен',
                'brand' => 'etteri',
                'device_family' => DeviceFamily::IPhone,
                'case_type' => null,
                'image_path' => 'images/products/etteri-camera-protector-iphone-17.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Защитете най-ценната част от вашия iPhone с безкомпромисно качество и стил.\nПротекторът за задна камера Etteri е проектиран специално за iPhone 17, като предлага пълно покритие на модула и индивидуална защита за всеки обектив.\nЗащо да изберете Etteri Full Camera Lens Guard?\nХибридна конструкция: Комбинация от закалено стъкло с твърдост 9H и лека, но здрава алуминиева рамка в наситен черен цвят.",
            ],
            [
                'name' => 'Стъклени рингове за камера',
                'slug' => 'camera-glass-rings-huawei-pura-80-ultra',
                'subtitle' => 'Huawei Pura 80 Ultra',
                'brand' => null,
                'device_family' => DeviceFamily::Huawei,
                'case_type' => null,
                'image_path' => 'images/products/camera-glass-rings-huawei-pura-80-ultra.png',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Ефектен стъклен протектор за задна камера изработен от висококачествено и удароустойчиво закалено стъкло.\nКомплектът включва самостоятелен протектор за всяка от вашите задни камери.\nПротекторите ще защитят камерата на вашето устройство и едновременно с това ще го направят стилно.\nПредпазват от счупване, евентуални удари и драскотини, както и от прашинки.\nИма много високо ниво на прозрачност и прилепва плътно върху камерата.",
            ],
            [
                'name' => '5D Full Cover стъклен протектор',
                'slug' => '5d-tempered-glass-redmi-note-15-pro-plus',
                'subtitle' => 'Xiaomi Redmi Note 15 Pro Plus',
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => null,
                'image_path' => 'images/products/5d-tempered-glass-redmi-note-15-pro-plus.png',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Модел: Full Glue Tempered Glass\nСъвместимост: Xiaomi Redmi Note 15 Pro Plus\nЦвят: безцветен с черен кант\n- Най-висок клас стъклено покритие за дисплея на вашето мобилно устройство.\n- Материал- изработен от закалено стъкло с твърдост 9H, предпазващо от надраскване и други евентуални щети.\n- 99% Прозрачност- стъкления протектор е почти незабележим поради високата си прозрачност и не влияе на качеството на дисплея.",
            ],
            [
                'name' => '5D Full Cover стъклен протектор',
                'slug' => '5d-tempered-glass-xiaomi-17t-pro',
                'subtitle' => 'Xiaomi 17T Pro · черен кант',
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => null,
                'image_path' => 'images/products/5d-tempered-glass-xiaomi-17t-pro.png',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Модел: Full Glue Tempered Glass\nСъвместимост: Xiaomi 17T Pro\nЦвят: безцветен с черен кант\n- Най-висок клас стъклено покритие за дисплея на вашето мобилно устройство.\n- Материал- изработен от закалено стъкло с твърдост 9H, предпазващо от надраскване и други евентуални щети.\n- 99% Прозрачност- стъкления протектор е почти незабележим поради високата си прозрачност и не влияе на качеството на дисплея.",
            ],
            [
                'name' => 'Комплект кейс и протектор',
                'slug' => 'kit-case-glass-galaxy-a37',
                'subtitle' => 'Samsung Galaxy A37 · тъмнозелен',
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/kit-case-glass-galaxy-a37.jpg',
                'price_in_stotinki' => 2495,
                'compare_at_price_in_stotinki' => 4990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Осигурете пълна 360-градусова защита за своя нов Samsung Galaxy A37 с нашия специално подбран комплект.\nКомбинацията от стилен тъмнозелен (Dark Green) нюанс и висококачествен скрийн протектор гарантира, че телефонът ви ще изглежда перфектно и ще бъде защитен от всеки ъгъл.\nТъмнозелен силиконов кейс (Soft-Touch)",
            ],
        ];
    }

    /**
     * Зарядни 220V.
     *
     * @return list<array<string, mixed>>
     */
    protected function mainsChargers(): array
    {
        return [
            [
                'name' => 'Зарядно устройство LDNIO Q7 GaN 65W с USB-C USB-A и USB Type-C към USB Type-C кабел',
                'slug' => 'zaryadno-ustroystvo-ldnio-q7-gan-65w-s-usb-c-usb-a-i-usb',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-ldnio-q7-gan-65w-s-usb-c-usb-a-i-usb.png',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Зарядно устройство LDNIO Q7 GaN 65W с USB-C USB-A и USB Type-C към USB Type-C кабел\nLDNIO Q7 GaN мрежово зарядно устройство 65W с USB-C, USB-A и USB Type-C към USB Type-C кабел\nLDNIO Q7 е компактно GaN мрежово зарядно устройство с максимална мощност 65W. Разполага с USB-C и USB-A изход и се предлага в комплект с USB Type-C към USB Type-C кабел. Подходящо е за бързо зареждане на смартфони, таблети, лаптопи и други съвместими устройства у дома, в офиса или по време на пътуване.\nВключен кабел: USB Type-C към USB Type-C, 1 m\nЗарядното устройство е съвместимо с широка гама устройства, включително:\nдруги устройства, които поддържат зареждане чрез USB-C или USB-A.\n1 бр. LDNIO Q7 GaN мрежово зарядно устройство 65W\n1 бр. USB Type-C към USB Type-C кабел 1 m.",
            ],
            [
                'name' => 'Зарядно устройство LDNIO Q6 GaN 45W с USB-C, USB-A и USB Type-C към USB Type-C кабел',
                'slug' => 'zaryadno-ustroystvo-ldnio-q6-gan-45w-s-usb-c-usb-a-i-usb',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-ldnio-q6-gan-45w-s-usb-c-usb-a-i-usb.png',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => 11990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Зарядно устройство LDNIO Q6 GaN 45W с USB-C, USB-A и USB Type-C към USB Type-C кабел\nLDNIO Q6 GaN мрежово зарядно устройство 45W с USB-C, USB-A и USB Type-C към USB Type-C кабел\nLDNIO Q6 е компактно GaN мрежово зарядно устройство с максимална мощност 45W. Разполага с USB-C и USB-A изход и се предлага в комплект с USB Type-C към USB Type-C кабел. Подходящо е за бързо зареждане на смартфони, таблети и други съвместими устройства у дома, в офиса или по време на пътуване.\nВключен кабел: USB Type-C към USB Type-C, 1 m\nЗарядното устройство е съвместимо с широка гама устройства, включително:\nдруги устройства, които поддържат зареждане чрез USB-C или USB-A.\n1 бр. LDNIO Q6 GaN мрежово зарядно устройство 45W\n1 бр. USB Type-C към USB Type-C кабел 1 m.",
            ],
            [
                'name' => 'Зарядно устройство LDNIO Q5 GaN 33W с USB-C USB-A и USB Type-C към USB Type-C кабел',
                'slug' => 'zaryadno-ustroystvo-ldnio-q5-gan-33w-s-usb-c-usb-a-i-usb',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-ldnio-q5-gan-33w-s-usb-c-usb-a-i-usb.png',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => 9990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Зарядно устройство LDNIO Q5 GaN 33W с USB-C USB-A и USB Type-C към USB Type-C кабел\nLDNIO Q5 GaN зарядно устройство 33W с USB-C, USB-A и USB Type-C към USB Type-C кабел\nLDNIO Q5 е компактно GaN мрежово зарядно устройство с максимална мощност 33W. Разполага с USB-C и USB-A изход и се предлага в комплект с USB Type-C към USB Type-C кабел. Подходящо е за бързо зареждане на смартфони, таблети и други съвместими устройства у дома, в офиса или по време на пътуване.\nВключен кабел: USB Type-C към USB Type-C, 1 m\nЗарядното устройство е съвместимо с широка гама устройства, включително:\nдруги устройства, които поддържат зареждане чрез USB-C или USB-A.\n1 бр. LDNIO Q5 GaN мрежово зарядно устройство 33W\n1 бр. USB Type-C към USB Type-C кабел 1 m.",
            ],
            [
                'name' => 'Оригинално зарядно адаптер за SAMSUNG 45W 220v Super Fast Charger Ep-Ta845xbe',
                'slug' => 'originalno-zaryadno-adapter-za-samsung-45w-220v-super',
                'subtitle' => 'Samsung S25 FE',
                'brand' => 'samsung',
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/originalno-zaryadno-adapter-za-samsung-45w-220v-super.jpg',
                'price_in_stotinki' => 5945,
                'compare_at_price_in_stotinki' => 11890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Оригинално зарядно адаптер за SAMSUNG 45W 220v Super Fast Charger Ep-Ta845xbe - Samsung S25 FE\nОригинален адаптер 220V Samsung Travel Adapter с поддръжка на бързо зареждане - Super Fast Charging и мощност от 45W.\nАдаптерът работи при напрежение в порядъка 100V-240V, 50-60Hz, 0.7A.\nБлагодарение на големия толеранс относно напреженията изисквани към захранващата мрежа може да го използвате навсякъде и във всяка точка на света.\nПоддържа технология за бързо зареждане - Super Fast Charging, патент на Samsung.\nИзходните(output) характеристики захранващи Вашия телефон сa:\n(Power Delivery Objects - PDO) 5.0V=3.0A, 9.0V=3.0А, 15V=3.0A, 20V=2.25A\n(Programmable Power Supply - PPS) 3.3V-11V=4.05A, 3.3V-16.0V=2.8A, 3.3V-21V=2.1A\nTелефонът Ви трябва да поддържа бързо зареждане, за да използвате този адаптер.\nТехнологията Super Fast Charging е пригодена основно за смартфони и други устройства на Samsun",
            ],
            [
                'name' => 'Оригинално Зарядно SAMSUNG 45W 220v Super Fast Charger с USB-C комплект с кабел Ep-Ta800xbe - Samsung S26 / S26 Plus / S26 Ultra',
                'slug' => 'originalno-zaryadno-samsung-45w-220v-super-fast-charger-s',
                'subtitle' => null,
                'brand' => 'samsung',
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/originalno-zaryadno-samsung-45w-220v-super-fast-charger-s.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Оригинално Зарядно SAMSUNG 45W 220v Super Fast Charger с USB-C комплект с кабел Ep-Ta800xbe - Samsung S26 / S26 Plus / S26 Ultra\nКомплект оригинално зарядно устройство - мрежови адаптер(45W) и кабел(USB Type-C/USB Type-C) за телефони Samsung поддържащи бързо зареждане - Super Fast Charging 2.0 (Power Delivery 3.0)\nАдаптерът работи при напрежение в порядъка 100V-240V, 50-60Hz, 0.7A. Благодарение на големия толеранс относно напреженията изисквани към захранващата мрежа може да го използвате навсякъде и във всяка точка на света. Поддържа технология за бързо зареждане - Super Fast Charging, патент на Samsung. Изходните(output) характеристики захранващи Вашия телефон сa:\n(Power Delivery Objects - PDO) 5.0V=3.0A, 9.0V=3.0А, 15V=3.0A, 20V=2.25A\n(Programmable Power Supply - PPS) 3.3V-11V=4.05A, 3.3V-16.0V=2.8A, 3.3V-21V=2.1A\nКабелът в комплекта е с накрайници USB Type-C/USB Type-C и дължина от 1",
            ],
            [
                'name' => 'Зарядно устройство адаптер 100W 2x USB-C 1x USB XO CE35 PD',
                'slug' => 'zaryadno-ustroystvo-adapter-100w-2x-usb-c-1x-usb-xo-ce35',
                'subtitle' => null,
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-adapter-100w-2x-usb-c-1x-usb-xo-ce35.jpg',
                'price_in_stotinki' => 7995,
                'compare_at_price_in_stotinki' => 15990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Зарядно устройство адаптер 100W 2x USB-C 1x USB XO CE35 PD\nМрежово зарядно устройство XO CE35 GaN, 100W, 2x USB-C, 1x USB-A\nМрежовото зарядно устройство XO CE35 е мощно, надеждно и компактно решение за едновременно захранване на всички ваши устройства.\nБлагодарение на усъвършенстваната GaN (галиев нитрид) технология, този адаптер комбинира огромна мощност от 100W с изключително ергономични размери.\nМоделът ви позволява да зареждате едновременно до три устройства – от взискателни лаптопи до таблети, смартфони и преносими конзоли – без компромис в скоростта или безопасността.\nМаксимална мощност 100W: Осигурява бързо и ефективно зареждане на енергоемки устройства като MacBook Pro/Air, лаптопи с Type-C захранване и флагмански смартфони.\nМултипортова функционалност: Разполага с два USB-C порта и един класически USB-A порт, елиминирайки нуждата от носене на множество адаптери.\nИнтелигентно раз",
            ],
            [
                'name' => 'Зарядно устройство комплект HOCO N75 65W 2x USB-C 1x USB-A + кабел USB-C - USB-C',
                'slug' => 'zaryadno-ustroystvo-komplekt-hoco-n75-65w-2x-usb-c-1x-usb',
                'subtitle' => null,
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-komplekt-hoco-n75-65w-2x-usb-c-1x-usb.jpg',
                'price_in_stotinki' => 5795,
                'compare_at_price_in_stotinki' => 11590,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Зарядно устройство комплект HOCO N75 65W 2x USB-C 1x USB-A + кабел USB-C - USB-C\nHOCO N75 е мощен комплект зарядно устройство с GaN технология и до 65W бързо зареждане, който осигурява ефективно захранване на смартфони, таблети и дори лаптопи.\nРазполага с 2 USB-C и 1 USB-A порт, позволявайки едновременно зареждане на няколко устройства, като автоматично разпределя мощността за оптимална работа.\nПоддържа технологии като Power Delivery и Quick Charge, а компактният дизайн го прави удобен за ежедневна употреба у дома, в офиса или при пътуване.",
            ],
        ];
    }

    /**
     * Зарядни за кола 12V.
     *
     * @return list<array<string, mixed>>
     */
    protected function carChargers(): array
    {
        return [
            [
                'name' => 'Зарядно устройство за кола адаптер XO CC58 95W 1x USB-C 1x USB-A',
                'slug' => 'zaryadno-ustroystvo-za-kola-adapter-xo-cc58-95w-1x-usb-c',
                'subtitle' => null,
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-za-kola-adapter-xo-cc58-95w-1x-usb-c.jpg',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => 9990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Бързо зарядно за автомобил XO CC58, 95W, 12V, USB-A, USB-C\nМоделът XO CC58 е мощно и компактно зарядно устройство за кола, което осигурява обща изходна мощност до 95W през двата си порта.\nТо е отличен избор за едновременно бързо зареждане на лаптоп (превод през USB-C) и смартфон или таблет в автомобила\nУстройството разпределя своята максимална мощност от 95W по следния начин:\nUSB-C порт (65W макс.): Поддържа бързо зареждане по стандартите PD3.0, PD2.0 и PPS. Подходящ е за захранване на лаптопи (например MacBook), таблети и съвременни смартфони.\nРежими на работа: 5V/3A, 9V/3A, 12V/3A, 15V/3A, 20V/3.25A. [, 3]\nUSB-A порт (30W макс.): Поддържа стандартите QC3.0, QC2.0, AFC и FCP за бързо зареждане на съвместими Android и други мобилни устройства.\nЕдновременно зареждане: Можете да зареждате две устройства на пълна мощност едновременно (65W + 30W), без те да си пречат или да забавят скоростта",
            ],
            [
                'name' => 'Зарядно устройство за кола комплект HOCO NZ16 PD 30W 1x USB-C с кабел USB-C',
                'slug' => 'zaryadno-ustroystvo-za-kola-komplekt-hoco-nz16-pd-30w-1x',
                'subtitle' => 'Lightning',
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-za-kola-komplekt-hoco-nz16-pd-30w-1x.jpg',
                'price_in_stotinki' => 3895,
                'compare_at_price_in_stotinki' => 7790,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Зарядно устройство за кола комплект HOCO NZ16 PD 30W 1x USB-C с кабел USB-C - Lightning\nКомпактен комплект за бързо зареждане в автомобил, включващ зарядно с USB-C порт и кабел USB-C към Lightning.\nПоддържа Power Delivery до 30W за ефективно и бързо зареждане на съвместими устройства, включително iPhone и други Apple продукти.\nОсигурява стабилна и безопасна работа, подходящ за ежедневна употреба и пътуване.",
            ],
            [
                'name' => 'FM Трансмитер за кола DUDAO R2Pro, 2x USB / 3.1A, с Bluetooth MP3 и зарядно за кола 3.1A',
                'slug' => 'fm-transmiter-za-kola-dudao-r2pro-2x-usb-3-1a-s-bluetooth',
                'subtitle' => null,
                'brand' => 'dudao',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/fm-transmiter-za-kola-dudao-r2pro-2x-usb-3-1a-s-bluetooth.jpg',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => 9990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "FM Трансмитер за кола DUDAO R2Pro, 2x USB / 3.1A, с Bluetooth MP3 и зарядно за кола 3.1A\n• Този FM трансмитер ви позволява, да споделяте любимата си музика от мобилния си телефон, с помощта на FM радиовълни. Просто свържете вашия телефон с FM трансмитера чрез Bluetooth, след това настройте на FM радио с ясна честота и се наслаждавайте на любимата си музика в движение\n• Бързо зареждане - наличните два USB изхода ви позволяват да зареждате две устройства едновременно.\n• Цифров LED дисплей - Той не само добавя модерен щрих към устройството, но също така показва текущото напрежение, което ви позволява лесно да наблюдавате състоянието на зареждане\n• Вграден Bluetooth FM предавател, можете да възпроизвеждате музика или да провеждате телефонни разговори със свободни ръце\n• Музика от USB флашка – можете лесно да възпроизвежда аудио файлове от свързана USB флашка\n• Функции: Bluetooth поддръжка, г",
            ],
            [
                'name' => 'FM Трансмитер за кола HOCO E86 Alegria с Bluetooth MP3 и зарядно за кола 48W / PD30W + QC3.0',
                'slug' => 'fm-transmiter-za-kola-hoco-e86-alegria-s-bluetooth-mp3-i',
                'subtitle' => null,
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/fm-transmiter-za-kola-hoco-e86-alegria-s-bluetooth-mp3-i.jpg',
                'price_in_stotinki' => 4745,
                'compare_at_price_in_stotinki' => 9490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "FM Трансмитер за кола HOCO E86 Alegria с Bluetooth MP3 и зарядно за кола 48W / PD30W + QC3.0\nПревърнете колата си в интелигентен хъб с Hoco E86 Alegria – компактно автомобилно устройство, което комбинира бързо двупортово зареждане (USB‑C PD30W + USB‑A QC3.0) с FM Bluetooth предавател. Можете да слушате музика от телефона си през високоговорителите на автомобила, да отговаряте на хендсфри разговори или да възпроизвеждате MP3 файлове директно от USB/TF. Bluetooth 5.4 чипът осигурява стабилни връзки, а LED екранът и RGB осветлението добавят модерен щрих към интериора.\nUSB‑C PD – до 30 W (5 V/3 A, 9 V/3 A, 12 V/2.5 A)\nUSB‑A QC – до 18 W (5 V/3 A, 9 V/2 A, 12 V/1.5 A)\nBluetooth 5.4, поддържа A2DP, AVRCP, хендсфри\nFM честота: 87.5–108 MHz, с обхват прибл. 5 м\nBluetooth обхват: до 10 м, честота 2.4 GHz\nМатериал: ABS, размери 84×47×54 мм, тегло 43 г\nСъхранение на музика: поддържа TF карта / USB ",
            ],
            [
                'name' => 'Зарядно устройство за кола адаптер BOROFONE BZ24 20W / Type-C to Type-C',
                'slug' => 'zaryadno-ustroystvo-za-kola-adapter-borofone-bz24-20w',
                'subtitle' => null,
                'brand' => 'borofone',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/zaryadno-ustroystvo-za-kola-adapter-borofone-bz24-20w.jpg',
                'price_in_stotinki' => 2995,
                'compare_at_price_in_stotinki' => 5990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Зарядно устройство за кола адаптер BOROFONE BZ24 20W / Type-C to Type-C\nBOROFONE BZ24 Clever автомобилно зарядно, вход DC12-24V, изход USB-A 18W (QC/FCP/AFC) + Type-C PD 20W, изработено от огнеустойчив PC и алуминиев сплав, размер 63×27мм, тегло 30г, комплект с 1м Type-C към Type-C кабел за бързо зареждане",
            ],
        ];
    }

    /**
     * Bluetooth слушалки.
     *
     * @return list<array<string, mixed>>
     */
    protected function wirelessHeadphones(): array
    {
        return [
            [
                'name' => 'Безжичен микрофон Hoco L21',
                'slug' => 'bezzhichen-mikrofon-hoco-l21',
                'subtitle' => 'USB-C с приемник',
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/bezzhichen-mikrofon-hoco-l21.jpg',
                'price_in_stotinki' => 3945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Безжичен микрофон Hoco L21 с USB-C приемник - компактно решение за видео записи, интервюта, онлайн срещи, стрийминг и съдържание за социални мрежи.\nБлагодарение на технологията ENC за намаляване на околния шум осигурява чист и ясен звук дори в по-шумна среда.\nУлавя гласа естествено и без необходимост от прецизно позициониране.\nРаботи чрез Plug & Play без нужда от допълнителни приложения и е съвместим с повечето смартфони и таблети с USB-C порт.",
            ],
            [
                'name' => 'Bluetooth хендсфри Hoco E57',
                'slug' => 'slushalka-hendsfri-hoco-e57',
                'subtitle' => 'Моно слушалка',
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/slushalka-hendsfri-hoco-e57.jpg',
                'price_in_stotinki' => 3895,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Bluetooth хендсфри слушалка Hoco E57 с Bluetooth 5.0 за стабилна и чиста връзка, подходяща за разговори, работа и шофиране.\nПоддържа свързване с 2 устройства едновременно , разполага с вграден микрофон и бутони за управление на музика и обаждания.\nЛек и удобен ергономичен дизайн с тегло само 10 г, батерия 170mAh с до 10 часа разговори и музика и до 200 часа standby режим.\nБранд: HOCO\nВид продукт: Хендсфри\nМатериал: Пластмаса\nСъвместим модел: Универсален",
            ],
            [
                'name' => 'Слушалки Hoco W35',
                'slug' => 'slushalki-hoco-w35-90h',
                'subtitle' => 'Bluetooth · до 90 ч.',
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/slushalki-hoco-w35-90h.jpg',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Hoco W35 са безжични слушалки с впечатляващ живот на батерията до 90 часа работа. Осигуряват стабилна Bluetooth връзка, удобни меки наушници и балансиран звук. Подходящи са за продължително слушане на музика, онлайн разговори и работа от разстояние.\nБранд: HOCO\nВерсия Bluetooth: 5.3\nВид продукт: Безжични слушалки\nМатериал: Метал, Пластмаса\nОбхват: 10 м\nСъвместим модел: Универсален\nСъвместима марка: Универсален\nТип: Over the ear\nТип свързване: Bluetooth",
            ],
            [
                'name' => 'Слушалки Hoco W35 Air 40h Bluetooth',
                'slug' => 'slushalki-hoco-w35-air-40h-bluetooth',
                'subtitle' => null,
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/slushalki-hoco-w35-air-40h-bluetooth.jpg',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => 11990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "- Hoco W35 Air са удобни и модерни Bluetooth слушалки, създадени да осигурят свобода и качествен звук във всяка ситуация.\n- Ергономичният им, прибиращ се и въртящ се дизайн позволява комфортно носене без притискане на ушите, дори при продължителна употреба.\n- Оборудвани са с Bluetooth версия 5.3 за стабилна и енергийно ефективна връзка и разполагат с мощна 400 mAh батерия, която осигурява до 40 часа време за музика или разговори и до 100 часа в режим на готовност.\n- Слушалките поддържат възпроизвеждане както чрез Bluetooth, така и чрез TF карта или AUX вход, което дава повече гъвкавост. 40-милиметровият говорител осигурява плътен и балансиран звук с добри ниски честоти, подходящ както за пътуване, така и за домашна употреба.",
            ],
            [
                'name' => 'Слушалки Bluetooth безжични Hoco TWS ANC ENC EQ21 с тъчскрийн',
                'slug' => 'slushalki-bluetooth-bezzhichni-hoco-tws-anc-enc-eq21-s',
                'subtitle' => null,
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/slushalki-bluetooth-bezzhichni-hoco-tws-anc-enc-eq21-s.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Bluetooth безжични слушалки Hoco TWS ANC ENC EQ21 с тъчскрийн\nБезжичните слушалки Hoco EQ21 предлагат модерни функции като активна шумопотискаща технология (ANC) и ENC за по-ясни разговори.\nОборудвани са с тъчскрийн управление върху кейса, което позволява лесен контрол на музиката и настройките.\nПодходящи са за ежедневна употреба, пътуване и работа.",
            ],
        ];
    }

    /**
     * Power Bank.
     *
     * @return list<array<string, mixed>>
     */
    protected function powerBanks(): array
    {
        return [
            [
                'name' => 'Power Bank Dudao K28',
                'slug' => 'power-bank-dudao-k28-10000',
                'subtitle' => '10000 mAh · MagSafe · 22.5W',
                'brand' => 'dudao',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/power-bank-dudao-k28-10000.jpg',
                'price_in_stotinki' => 7995,
                'compare_at_price_in_stotinki' => 15990,
                'is_new' => false,
                'is_featured' => false,
                'description' => 'Външната батерия Dudao K28 съчетава елегантен външен вид, солиден метален корпус и модерна функционалност.',
            ],
            [
                'name' => 'Power Bank XO PB313',
                'slug' => 'power-bank-xo-pb313-20000',
                'subtitle' => '20000 mAh',
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/power-bank-xo-pb313-20000.jpeg',
                'price_in_stotinki' => 6845,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Външната батерия XO PB313 20000 mAh осигурява надеждно преносимо захранване навсякъде.\nУстройството притежава оптимална изходяща мощност от 10W.\nМоделът разполага с два USB-A порта за едновременно зареждане.\nВграденият литиево-полимерен акумулатор (Li-Pol) гарантира изключително дълъг живот.\nОсновни предимства\nКапацитет от 20000 mAh.\nДва USB-A изходни порта.\nИнтелигентен Smart IC чип.\nЧетири LED индикатора за заряд.\nОгнеупорни ABS и PC материали.",
            ],
            [
                'name' => 'Външна батерия Power Bank Dudao K26 10000mAh 20W USB-A / USB-C / MagSafe Wireless / PD/20W USB-C порт и безжично зареждане с MagSafe / Magnetic Wireless Charging Power Bank Dudao K26 10000mAh 20W USB-A / USB-C / MagSafe',
                'slug' => 'vanshna-bateriya-power-bank-dudao-k26-10000mah-20w-usb-a',
                'subtitle' => 'Бяла',
                'brand' => 'dudao',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/vanshna-bateriya-power-bank-dudao-k26-10000mah-20w-usb-a.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Външна батерия Power Bank Magnetic Wireless Charging Dudao K26 10000mAh 20W USB-A / USB-C / MagSafe - Бяла\nПреносима батерия с безжично зареждане MagSafe\nБатерията е с капацитет от 10 000 mAh и максимална мощност от 20 W, можете да зареждате устройствата си по-бързо и по-ефективно.\nПоддръжката на протоколи Power Delivery и Quick Charge осигурява светкавично бързо зареждане, а съвместимостта с MagSafe позволява безжично зареждане до 15 W.\nОборудвана с два USB-A и USB-C порта, тази преносима батерия гарантира многофункционалност и гъвкавост.\nКомпактна, лека и стилна – перфектният ежедневен спътник\n20W мощност за светкавично бързо зареждане -осигурете на устройствата си максимална скорост на зареждане с мощност 20 W.\nСпестете време и се насладете на по-дълъг живот на батерията, без да е необходимо да включвате устройството в контакт.\nПоддръжка на Power Delivery и бързо зареждане - външната ",
            ],
            [
                'name' => 'Външна батерия Power bank XO-PB301 / 2xUSB 2A / 10000mAh',
                'slug' => 'vanshna-bateriya-power-bank-xo-pb301-2xusb-2a-10000mah',
                'subtitle' => 'Бяла',
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/vanshna-bateriya-power-bank-xo-pb301-2xusb-2a-10000mah.jpg',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => 9990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Външна батерия Power bank XO-PB301 / 2xUSB 2A / 10000mAh - Бяла\n- Преносима външна батерия XO PB301 с капацитет 10 000mAh е лесно преносимо устройство, което се използва за зареждане на различни електронни апарати, като мобилни телефони, таблети и други.\n- Разполага с 1 х Micro USB вход, 1 x USB-C вход и 2 x USB-A изхода.\n- Батерията може да зарежда няколко устройства едновременно и притежава дисплей, който показва индикацията за степента на заряд.\n- Притежава Slim дизайн, което я прави изключително компактна, лека и много удобна за ежедневна употреба. .\nSlim дизайн: Компактна, лека и много удобна за ежедневна употреба.\nLED индикатор, показващ заряда на батерията.",
            ],
            [
                'name' => 'Външна батерия Power bank XO-PB312 / 2xUSB 10W / 10000mAh',
                'slug' => 'vanshna-bateriya-power-bank-xo-pb312-2xusb-10w-10000mah',
                'subtitle' => null,
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/vanshna-bateriya-power-bank-xo-pb312-2xusb-10w-10000mah.jpg',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => 9890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Външна батерия Power bank XO-PB312 / 2xUSB 10W / 10000mAh\nВъншна батерия XO PB312 – компактен Power Bank с капацитет 10000 mAh\nXO PB312 е стилна и надеждна външна батерия с капацитет 10000 mAh, създадена за хора, които ценят практичността, компактния дизайн и сигурното зареждане навсякъде. Моделът е част от серията Slim Power Bank устройства на XO – отличава се с минималистичен профил, ниско тегло и стабилна производителност, подходяща за ежедневна употреба у дома, в офиса или по време на пътувания.\nВъншната батерия XO PB312 осигурява стабилно и безопасно зареждане с мощност до 10W. Благодарение на два USB-A изхода и един USB-C вход, можете да зареждате едновременно две устройства – телефон, слушалки, смарт часовник или таблет. Технологията за интелигентно разпознаване на устройството гарантира оптимално подаване на ток и напрежение, за да се избегне прегряване или презареждане.\nXO PB312",
            ],
        ];
    }

    /**
     * Смарт часовници.
     *
     * @return list<array<string, mixed>>
     */
    protected function smartWatches(): array
    {
        return [
            [
                'name' => 'Смарт часовник Valdus VS14',
                'slug' => 'smart-watch-valdus-vs14',
                'subtitle' => 'Черен',
                'brand' => 'valdus',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/smart-watch-valdus-vs14.jpg',
                'price_in_stotinki' => 7995,
                'compare_at_price_in_stotinki' => 15990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Един от най-големите проблеми, с които се сблъскват потребителите на смарт часовници, е краткият живот на батерията. VALDUS решава този проблем с мощна батерия от 280 mAh, която осигурява до 7 дни употреба и 10-12 дни в режим на готовност с едно зареждане.\nЗареждането е лесно благодарение на магнитното зареждане - просто го закачете и устройството се зарежда за по-малко от 3 часа.",
            ],
            [
                'name' => 'Детски LED часовник Stitch',
                'slug' => 'detski-led-chasovnik-stitch',
                'subtitle' => 'За деца',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/detski-led-chasovnik-stitch.jpg',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "- Детският LED часовник със Стич е забавен и стилен аксесоар, перфектен за малките фенове на обичания извънземен герой.\n- Със свежи цветове и дизайн, вдъхновен от чаровния Стич, този часовник добавя весело настроение и характер към всяка детска визия.\n- Благодарение на LED дисплея с 12-часов формат, децата могат лесно да разпознават часа и датата, което го прави не само модерен, но и образователен.",
            ],
            [
                'name' => 'Детски LED часовник Спайдърмен',
                'slug' => 'detski-led-chasovnik-spaydarmen-spiderman',
                'subtitle' => 'Spiderman',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/detski-led-chasovnik-spaydarmen-spiderman.jpg',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => 9890,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Детски LED часовник Спайдърмен - Spiderman\n- Детският LED часовник с дизайн на Spiderman е впечатляващ и практичен аксесоар, създаден специално за малките почитатели на любимия супергерой.\n- Със стилна визия и ярки цветове, вдъхновени от Спайдърмен, този часовник придава супергеройско излъчване на всяка детска ръка.\n- LED дисплеят с 12-часов формат позволява на децата лесно да следят часа и датата, като съчетава забавлението с образователна стойност.\n- Регулируемата каишка гарантира комфортно и сигурно прилягане, а здравият корпус го прави устойчив на игри и активност.\n- Подходящ както за ежедневието, така и за подарък по специален повод, този часовник е идеално допълнение към аксесоарите на всяко дете, което мечтае да бъде като Spiderman.\n- Часовникът пристига с включена батерия.",
            ],
            [
                'name' => 'Детски LED часовник Пес Патрул',
                'slug' => 'detski-led-chasovnik-pes-patrul-paw-patrol',
                'subtitle' => 'Paw Patrol',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/detski-led-chasovnik-pes-patrul-paw-patrol.jpg',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => 9890,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Детски LED часовник Пес Патрул - Paw Patrol\n- Детският LED часовник с героите от Paw Patrol е едновременно забавен и полезен аксесоар, създаден специално за почитателите на популярната анимация.\n- Със своя цветен и атрактивен дизайн, вдъхновен от смелите кученца, този часовник внася настроение и индивидуалност във всяка детска визия.\n- Оборудван с ясен LED дисплей и 12-часов формат, той помага на децата лесно да следят часа и датата, като същевременно насърчава тяхната самостоятелност и обучение.\n- Регулируемата каишка осигурява комфортно и стабилно прилягане, а здравата изработка гарантира дълготрайна употреба дори при интензивни игри.\n- Идеален както за всекидневието, така и за специални моменти, този часовник е чудесен избор за подарък на всяко дете, което обожава приключенията на Paw Patrol.\n- Часовникът пристига с включена батерия.",
            ],
        ];
    }

    /**
     * Аксесоари за часовници.
     *
     * @return list<array<string, mixed>>
     */
    protected function watchAccessories(): array
    {
        return [
            [
                'name' => 'Dudao A12H безжично зарядно за смарт часовници Huawei / Dudao A12H Wireless Charger Huawei Smartwatches',
                'slug' => 'dudao-a12h-bezzhichno-zaryadno-za-smart-chasovnitsi',
                'subtitle' => null,
                'brand' => 'dudao',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/dudao-a12h-bezzhichno-zaryadno-za-smart-chasovnitsi.png',
                'price_in_stotinki' => 3495,
                'compare_at_price_in_stotinki' => 6990,
                'is_new' => true,
                'is_featured' => true,
                'description' => null,
            ],
            [
                'name' => 'Оригинално безжично магнитно зарядно за смарт / smart / часовник Samsung Galaxy Watch / Fast Wireless Charger USB-C',
                'slug' => 'originalno-bezzhichno-magnitno-zaryadno-za-smart-smart',
                'subtitle' => 'черно',
                'brand' => 'samsung',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/originalno-bezzhichno-magnitno-zaryadno-za-smart-smart.jpg',
                'price_in_stotinki' => 4495,
                'compare_at_price_in_stotinki' => 8990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Оригинално безжично магнитно зарядно за смарт часовник Samsung Galaxy Watch / Fast Wireless Charger USB-C\n- Съвместимо с Galaxy Watch 6 Classic, Watch 5, Watch 5 Pro",
            ],
            [
                'name' => 'Безжично магнитно зарядно за смарт часовник Huawei и Honor с USB кабел',
                'slug' => 'bezzhichno-magnitno-zaryadno-za-smart-chasovnik-huawei-i',
                'subtitle' => 'черен',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/bezzhichno-magnitno-zaryadno-za-smart-chasovnik-huawei-i.jpg',
                'price_in_stotinki' => 3445,
                'compare_at_price_in_stotinki' => 6890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Безжично магнитно зарядно за смарт часовник Huawei и Honor с USB кабел - черен\nДизайнът на безжичното зарядно е силен магнетизъм което помага да се запази зарядното устройство на място за надеждно зареждане.\nМоже да бъде свързано към USB източник на захранване на компютър, лаптоп, преносим компютър, защото този метод на зареждане може да ви осигури висока ефективност и висока стабилност на зареждането.\nКабелът за зареждане ще отговори на всяка ваша нужда у дома, в офиса или по време на пътуване.",
            ],
            [
                'name' => 'Универсален магнитен кабел за безжично зареждане на смарт часовник Huawei HW-6',
                'slug' => 'universalen-magniten-kabel-za-bezzhichno-zarezhdane-na',
                'subtitle' => null,
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/universalen-magniten-kabel-za-bezzhichno-zarezhdane-na.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Магнитен USB кабел за зареждане на Huawei Watch Fit/Fit 2/Fit 3, Huawei Band 6 7 8 9, Honor Band 6 7 8 9\nПоддържайте вашия Huawei Band 6 винаги зареден и готов за употреба със зарядно устройство, създадено с внимание към детайлите.\nТова зарядно устройство предлага надеждност и удобство за ежедневно ползване.\nСъвместимост: Зарядното е проектирано специално за Huawei Band 6, осигурявайки оптимално зареждане и защита за вашия смарт часовник.\nУдобен дизайн: С компактния и лек дизайн, зарядното е лесно за носене навсякъде, където отидете. Перфектно е за използване както у дома, така и в офиса или по време на пътуване.\nДълъг кабел: Дългият 1 метър кабел ви дава гъвкавост и удобство при зареждане, без да се налага да се борите с къси кабели.\nСигурно зареждане: Осигурява стабилно и безопасно зареждане, предпазвайки вашето устройство от прегряване, пренапрежение и късо съединение.\nЛесна употреба:",
            ],
            [
                'name' => 'Силиконова каишка за Apple Watch 38мм / 40мм',
                'slug' => 'silikonova-kaishka-za-apple-watch-38mm-40mm-chervena',
                'subtitle' => 'червена',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/silikonova-kaishka-za-apple-watch-38mm-40mm-chervena.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => 4490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Силиконова каишка за Apple Watch 38мм / 40мм - червена\nСтилна, едноцветна силиконова каишка за часовник.\nИзработена е от висококачествен, издръжлив и приятен на допир силиконов материал, който не дразни кожата.\nСмяната е изключително лесна, като не са необходими инструменти.\nКаишката може да бъде регулирана според големината на китката.\nЗакопчава се сигурно и удобно, като приляга нежно по ръката.\nКаишката има матов завършек, който допринася за отличителната ѝ визия.\nИзключително мека на допир и удобна за ежедневно носене.\nСъвместима е с моделите часовници: Apple Watch 38мм / 40мм",
            ],
        ];
    }

    /**
     * Дамски парфюми.
     *
     * @return list<array<string, mixed>>
     */
    protected function womensPerfumes(): array
    {
        return [
            [
                'name' => 'Asdaaf Ameerat Al Arab Prive Rose',
                'slug' => 'asdaaf-ameerat-al-arab-prive-rose-edp-100',
                'subtitle' => 'Дамски · EDP 100 мл.',
                'brand' => 'asdaaf',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/asdaaf-ameerat-al-arab-prive-rose-edp-100.png',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => 9990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Вдъхновен от Parfums de Marly / Delina Exclusif\nПроизводител: Asdaaf\nТип: Парфюмна вода\nКоличество: 60мл.\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nАроматни Нотки\n- Основни нотки: Кехлибар, сандалово дърво, боб Тонка\n- Средни нотки: Бял мускус, гардения, жасмин, иланг иланг, лилия, роза\n- Връхни нотки: Грозде, портокал, ягода",
            ],
            [
                'name' => 'Lattafa Ajwad Pink to Pink',
                'slug' => 'lattafa-ajwad-pink-to-pink-edp-60',
                'subtitle' => 'Дамски · EDP 60 мл.',
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/lattafa-ajwad-pink-to-pink-edp-60.png',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Вдъхновен от Oud Maracujá / Maison Crivelli\nПроизводител: Lattafa\nТип: Парфюмна вода\nКоличество: 60мл.\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nАроматни Нотки\n- Връхни нотки - розов грейпфрут, розов пипер, малина, гуава;\n- Средни нотки - роза, божур, магнолия;\n- Базови нотки - мускус, кожа, ванилия, амбра, мъх.",
            ],
            [
                'name' => 'Lattafa Ana Abiyedh Coral',
                'slug' => 'lattafa-ana-abiyedh-coral-edp-60',
                'subtitle' => 'Дамски · EDP 60 мл.',
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/lattafa-ana-abiyedh-coral-edp-60.png',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Производител: Lattafa\nВдъхновен от Wavechild / Room 1015\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\n- Тип - Парфюмна вода\n- Количество - 60мл.\nLattafa Ana Abiyedh Coral е завладяващ унисекс аромат, който излъчва топлина и елегантност.",
            ],
            [
                'name' => 'Дамски Арабски Парфюм Asdaaf Sa\'ud EDP 100 мл. / Вдъхновен от Parfums de Marly / Delina Exclusif',
                'slug' => 'damski-arabski-parfyum-asdaaf-sa-039-ud-edp-100-ml',
                'subtitle' => null,
                'brand' => 'asdaaf',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/damski-arabski-parfyum-asdaaf-sa-039-ud-edp-100-ml.jpeg',
                'price_in_stotinki' => 7945,
                'compare_at_price_in_stotinki' => 15890,
                'is_new' => true,
                'is_featured' => true,
                'description' => null,
            ],
        ];
    }

    /**
     * Мъжки парфюми.
     *
     * @return list<array<string, mixed>>
     */
    protected function mensPerfumes(): array
    {
        return [
            [
                'name' => 'Ombre Oud Intense Black',
                'slug' => 'ombre-oud-intense-black-edp-100',
                'subtitle' => 'Мъжки · EDP 100 мл.',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/ombre-oud-intense-black-edp-100.jpg',
                'price_in_stotinki' => 8995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Производител: Armaf\nВдъхновен от Ombre nomade/ LOUIS VUITTON\nАроматен профил:\nВръхни нотки\nКасис, маракуя, праскова, роза, тагетис\nСредни нотки\nБреза, тамян, шафран\nБазови нотки\nАмбър, бензоин, уд, пачули, сандалово дърво, ванилия",
            ],
            [
                'name' => 'French Avenue Spectre Ghost',
                'slug' => 'french-avenue-spectre-ghost-edp-80',
                'subtitle' => 'Мъжки · EDP 80 мл.',
                'brand' => 'french-avenue',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/french-avenue-spectre-ghost-edp-80.jpg',
                'price_in_stotinki' => 11945,
                'compare_at_price_in_stotinki' => 23890,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Производител: Fragrance World\nFrench Avenue Spectre Ghost е елегантен и мистериозен аромат с джинджифил, роза, касис и ванилия. Баланс между свежест и топлина - съвременен и завладяващ.\nВдъхновен от Ani / Nishane\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nАроматен профил:\nВръхни нотки:",
            ],
            [
                'name' => 'Fragrance World Proud of You Amber',
                'slug' => 'fragrance-world-proud-of-you-amber-edp-100',
                'subtitle' => 'Мъжки · EDP 100 мл.',
                'brand' => 'fragrance-world',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/fragrance-world-proud-of-you-amber-edp-100.jpg',
                'price_in_stotinki' => 7945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Производител: Fragrance World\nFragrance World Proud of You Amber - топъл, елегантен аромат с пикантни нотки, златист кехлибар и дървесна мекота. Модерен и дълготраен.\nВдъхновен от Emporio Armani Stronger With You Amber / Giorgio Armani\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nАроматен профил:\nВръхни нотки:",
            ],
            [
                'name' => 'Мъжки Арабски Парфюм - Fragrance World Abraaj Revere EDP 100 мл. / Вдъхновен от Honour Man / Amouage',
                'slug' => 'mazhki-arabski-parfyum-fragrance-world-abraaj-revere-edp',
                'subtitle' => null,
                'brand' => 'fragrance-world',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/mazhki-arabski-parfyum-fragrance-world-abraaj-revere-edp.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Мъжки Парфюм - Fragrance World Abraaj Revere EDP 100 мл.\nFragrance World Abraaj Revere е модерен и елегантен мъжки аромат с пикантен старт, зелено-флорална сърцевина и топъл дървесно-кехлибарен финал.\n100% оригинални продукти с доказан произход!\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nМадагаскарски розов пипер – Игрив и леко пикантен, носи свежа, ароматна топлина и модерна енергия още от първите секунди.\nГераниум – Зелено-флорален и леко ментолов, придава елегантен и чист характер.\nЕлеми – Смолист и леко лимонов, обогатява аромата със суха, балсамова свежест.\nИндийско орехче – Пикантен и леко дървесен, внася топлина и подправъчна дълбочина.\nВетивер – Земен и сух, носи естествена мъжествена елегантност.\nМускус – Мек и чувствен, придава гладкост и трайност.",
            ],
        ];
    }

    /**
     * Унисекс парфюми.
     *
     * @return list<array<string, mixed>>
     */
    protected function unisexPerfumes(): array
    {
        return [
            [
                'name' => 'Lattafa Khamrah Dukhan',
                'slug' => 'lattafa-khamrah-dukhan-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/lattafa-khamrah-dukhan-edp-100.jpg',
                'price_in_stotinki' => 7495,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Производител: Lattafa\nКоличество - 100мл.\nСъстав:\n- Връхни нотки - пименто, подправки, мандарина;\n- Средни нотки - лабданум, тамян, пачули, портокалов цвят;\n- Базови нотки - тютюн, пралина, зърна тонка, кехлибар, бензоин.",
            ],
            [
                'name' => 'Lattafa Victoria',
                'slug' => 'lattafa-victoria-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/lattafa-victoria-edp-100.jpeg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Вдъхновен от Devotion / Dolce & Gabbana\nПроизводител: Lattafa\n- Lattafa Victoria е нежен гурме аромат с лимонов пай, елегантно нероли и топла ванилия.\n- Сладко-цитрусова изтънченост, напомняща на лек и кремообразен десерт.\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nLattafa Victoria\nАроматен профил:",
            ],
            [
                'name' => 'Lattafa Pride Al Qiam Gold',
                'slug' => 'lattafa-pride-al-qiam-gold-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/lattafa-pride-al-qiam-gold-edp-100.jpg',
                'price_in_stotinki' => 7995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Вдъхновен от Ombre Nomade Louis Vuitton\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nПроизводител: Lattafa Pride\nКоличество - 100мл.\nСъстав\nВръхни нотки: Малина и Шафран\nСредни нотки: Кожа и Пачули\nБазови нотки: Уд, Кехлибар, Бензоин, Гваяково дърво и Ветивер",
            ],
            [
                'name' => 'Lattafa Ishq Al Shuyukh Gold',
                'slug' => 'lattafa-ishq-al-shuyukh-gold-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/lattafa-ishq-al-shuyukh-gold-edp-100.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Вдъхновен от Rosendo Mateu Nº 5 Elixir / Rosendo Mateu Olfactive Expressions\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nПроизводител: Lattafa\nНаличност - В наличност\nАроматен профил и нотки\nIshq Al Shuyukh Gold е сложен и многопластов аромат, който се развива красиво върху кожата:",
            ],
            [
                'name' => 'Унисекс Арабски Парфюм Lattafa Give Me Gourmand Berry On Top Парфюм EDP 75ml',
                'slug' => 'uniseks-arabski-parfyum-lattafa-give-me-gourmand-berry-on',
                'subtitle' => null,
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/uniseks-arabski-parfyum-lattafa-give-me-gourmand-berry-on.png',
                'price_in_stotinki' => 7820,
                'compare_at_price_in_stotinki' => 15640,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Унисекс Арабски Парфюм Lattafa Give Me Gourmand Berry On Top Eau de Parfum - Парфюмна вода за жени 75 мл\nLattafa Give Me Gourmand Berry On Top EDP е унисекс парфюмна вода от арабската ароматна къща Lattafa като част от колекцията Give Me Gourmand.\nТози аромат привлича със своята сладка, плодовo-кремообразна и гурме интерпретация, вдъхновена от десертни удоволствия като ягодов крем и сладки деликатеси.\nBerry On Top е създаден за хора, които обичат игриви, радостни и вкусово-вдъхновени ухания, които оставят приятно и привлекателно впечатление.\nПарфюмна вода /Eau De Parfum/ унисекс с плодово-гурме и леко цветен характер, който съчетава сочни ягодови нотки със сладки и кремообразни нюанси за мек, комфортен и устойчив шлейф.\nКомпозицията се разгръща със свежи и сочни върхови нотки от ягода и крем Chantilly, които плавно преминават в сладка, плодовo-цветна сърцевина от ягодово сладко, захар и ",
            ],
            [
                'name' => 'Унисекс Арабски Парфюм Lattafa Give Me Gourmand Whipped Pleasure Парфюм EDP 75ml.',
                'slug' => 'uniseks-arabski-parfyum-lattafa-give-me-gourmand-whipped',
                'subtitle' => null,
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/uniseks-arabski-parfyum-lattafa-give-me-gourmand-whipped.jpg',
                'price_in_stotinki' => 7820,
                'compare_at_price_in_stotinki' => 15640,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Унисекс Арабски Парфюм Lattafa Give Me Gourmand Whipped Pleasure Eau de Parfum - Парфюмна вода за жени 75 мл\nLattafa Give Me Gourmand Whipped Pleasure e парфюмна вода (Eau de Parfum) от Lattafa, представен като част от колекцията Give Me Gourmand – гурмански аромати със сладко и кремообразно излъчване.\nТози аромат пленява сетивата с апетитни акценти и уютна, сладка топлина, която носи усещане за изискан десерт, перфектен за усещане на комфорт и чувственост.\nEau de Parfum с ориентално-гурмански, сладък и пленяващ профил, създаден за любителите на аромати с кулинарни, кремообразни и обгръщащи нотки.\nПредлага усещане за уют и индулгенция, което прави аромата много подходящ за по-специални вечери и срещи.\nВръхни нотки: Карамел, солен карамел, пуканки\nБазови нотки: Хабе тонка, бензоин, мускус, амброфикс\nАпетитно сладко-солено начало с карамелизиран попкорн и солен карамел – като кулинарна нас",
            ],
            [
                'name' => 'Унисекс Арабски Парфюм Lattafa Give Me Gourmand Vanilla Freak EDP 75ml',
                'slug' => 'uniseks-arabski-parfyum-lattafa-give-me-gourmand-vanilla',
                'subtitle' => null,
                'brand' => 'lattafa',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/uniseks-arabski-parfyum-lattafa-give-me-gourmand-vanilla.png',
                'price_in_stotinki' => 7820,
                'compare_at_price_in_stotinki' => 15640,
                'is_new' => false,
                'is_featured' => false,
                'description' => "LATTAFA Give Me Gourmand Vanilla Freak EDP 75 ml - Унисекс Арабски Парфюм\nLattafa Give Me Gourmand Vanilla Freak EDP е унисекс парфюмна вода от арабската марка Lattafa, част от сладката и апетитна колекция *Give Me Gourmand*.\nТози аромат е истинско празненство на ванилията — интензивна, кадифено-кремообразна и гурме-вдъхновена композиция, която улавя същността на десертни удоволствия с топли и съблазнителни акценти.\nVanilla Freak е създаден за любителите на сладки, уютни и комфортни ухания, които искат аромат с характер, плътност и удоволствие.\nПарфюмна вода /Eau De Parfum/ унисекс с гурме-ориенталски и ванилово-кремообразен характер, който комбинира наситени, сладки и топли акценти с дървесно-амброва база за дълготраен, уютен и съблазнителен шлейф.\nКомпозицията започва със сладки, кремообразни и топли върхови нотки на ванилия, които плавно се развиват към по-богати гурме нюанси с подпра",
            ],
        ];
    }

    /**
     * Твърди гърбове.
     *
     * @return list<array<string, mixed>>
     */
    protected function hardBacks(): array
    {
        return [
            [
                'name' => 'Кейс Slide Camera с пръстен',
                'slug' => 'slide-camera-case-redmi-15',
                'subtitle' => 'Xiaomi Redmi 15 · черен',
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => CaseType::Shockproof,
                'image_path' => 'images/products/slide-camera-case-redmi-15.jpg',
                'price_in_stotinki' => 2845,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Предпазва телефона ви от удари и изпускания\nЗащитава камерата от удари и от записване на нежелано видео или снимки\nМагнитна част за захващане към стойки на автомобили\nСпециално покритие против плъзгане и надраскване\nПрахоустойчив - не събира по себе си прах и други дребни частици\nПръстен, който може да се използва за стойка в хоризонтално и вертикално положение",
            ],
            [
                'name' => 'Гръб Metal TPU Case',
                'slug' => 'metal-tpu-case-redmi-note-14',
                'subtitle' => 'Xiaomi Redmi Note 14 · черен / синьо',
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => CaseType::Hard,
                'image_path' => 'images/products/metal-tpu-case-redmi-note-14.jpg',
                'price_in_stotinki' => 2495,
                'compare_at_price_in_stotinki' => 4990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "- Корпусът е изработен от висококачествена алуминиева сплав, TPU и PC, предлагайки изтънчен дизайн.\n- Прецизно проектиран с детайлни метални акценти и елегантен, брониран външен вид за модерен и стилен вид.\n- Здравата конструкция осигурява превъзходна защита срещу падания, удари и драскотини, гарантирайки, че телефонът ви ще остане сигурен.\n- Повдигнатите с 0,3 мм ръбове около лещите на камерата предпазват от удари и предпазват лещите ви от надраскване.",
            ],
        ];
    }

    /**
     * USB кабели.
     *
     * @return list<array<string, mixed>>
     */
    protected function cables(): array
    {
        return [
            [
                'name' => 'Кабел LDNIO LC351C USB Type-C към USB Type-C кабел 240W',
                'slug' => 'kabel-ldnio-lc351c-usb-type-c-kam-usb-type-c-kabel-240w',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/kabel-ldnio-lc351c-usb-type-c-kam-usb-type-c-kabel-240w.png',
                'price_in_stotinki' => 2895,
                'compare_at_price_in_stotinki' => 5790,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Кабел LDNIO LC351C USB Type-C към USB Type-C кабел 240W 1 m\nLDNIO LC351C USB Type-C към USB Type-C кабел 240W 1 m\nLDNIO LC351C е USB Type-C към USB Type-C кабел, предназначен за бързо зареждане на съвместими устройства с мощност до 240W. Кабелът поддържа стандарта USB Power Delivery 3.1 (PD 3.1) и е подходящ за зареждане на смартфони, таблети, лаптопи и други устройства с USB Type-C порт.\nСтандарт за бързо зареждане: USB Power Delivery 3.1 (PD 3.1)\nКабелът е съвместим с всички устройства, оборудвани с USB Type-C порт, включително:\n1 бр. LDNIO LC351C USB Type-C към USB Type-C кабел 1 m",
            ],
            [
                'name' => 'Кабел LDNIO LC901i USB Type-C към Lightning кабел 30W',
                'slug' => 'kabel-ldnio-lc901i-usb-type-c-kam-lightning-kabel-30w',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/kabel-ldnio-lc901i-usb-type-c-kam-lightning-kabel-30w.png',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Кабел LDNIO LC901i USB Type-C към Lightning кабел 30W\nLDNIO LC901i USB Type-C към Lightning кабел 30W 1 m\nLDNIO LC901i е USB Type-C към Lightning кабел, предназначен за бързо зареждане на съвместими устройства на Apple с мощност до 30W. Подходящ е за зареждане на iPhone, iPad и други устройства с Lightning конектор.\nКабелът е съвместим с всички устройства, оборудвани с Lightning порт, включително:\n1 бр. LDNIO LC901i USB Type-C към Lightning кабел 1 m",
            ],
            [
                'name' => 'Кабел LDNIO LC901C USB Type-C към USB Type-C кабел 65W',
                'slug' => 'kabel-ldnio-lc901c-usb-type-c-kam-usb-type-c-kabel-65w',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/kabel-ldnio-lc901c-usb-type-c-kam-usb-type-c-kabel-65w.png',
                'price_in_stotinki' => 2645,
                'compare_at_price_in_stotinki' => 5290,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Кабел LDNIO LC901C USB Type-C към USB Type-C кабел 65W 1 m.\nLDNIO LC901C USB Type-C към USB Type-C кабел 65W 1 m\nLDNIO LC901C е USB Type-C към USB Type-C кабел, предназначен за бързо зареждане на съвместими устройства с мощност до 65W. Подходящ е за зареждане на смартфони, таблети, лаптопи и други устройства с USB Type-C порт.\nКабелът е съвместим с всички устройства, оборудвани с USB Type-C порт, включително:\n1 бр. LDNIO LC901C USB Type-C към USB Type-C кабел 1 m",
            ],
            [
                'name' => 'Кабел LDNIO LS901 USB-A към USB Type-C кабел 25W',
                'slug' => 'kabel-ldnio-ls901-usb-a-kam-usb-type-c-kabel-25w',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/kabel-ldnio-ls901-usb-a-kam-usb-type-c-kabel-25w.png',
                'price_in_stotinki' => 1945,
                'compare_at_price_in_stotinki' => 3890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Кабел LDNIO LS901 USB-A към USB Type-C кабел 25W 1 m.\nLDNIO LS901 USB-A към USB Type-C кабел 25W 1 m\nLDNIO LS901 е USB-A към USB Type-C кабел, предназначен за зареждане на съвместими мобилни устройства с мощност до 25W. Кабелът е подходящ за ежедневна употреба у дома, в офиса или в автомобила.\nКабелът е съвместим с всички устройства, оборудвани с USB Type-C порт, включително:\n1 бр. LDNIO LS901 USB-A към USB Type-C кабел 1 m",
            ],
            [
                'name' => 'Кабел LDNIO LS901 USB-A към Micro USB кабел 25W',
                'slug' => 'kabel-ldnio-ls901-usb-a-kam-micro-usb-kabel-25w',
                'subtitle' => null,
                'brand' => 'ldnio',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/kabel-ldnio-ls901-usb-a-kam-micro-usb-kabel-25w.png',
                'price_in_stotinki' => 1945,
                'compare_at_price_in_stotinki' => 3890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Кабел LDNIO LS901 USB-A към Micro USB кабел 25W 1 m.\nLDNIO LS901 USB-A към Micro USB кабел 25W 1 m\nLDNIO LS901 е USB-A към Micro USB кабел, предназначен за зареждане на съвместими мобилни устройства с мощност до 25W. Кабелът е подходящ за ежедневна употреба у дома, в офиса или в автомобила.\nКабелът е съвместим с всички устройства, оборудвани с Micro USB порт, включително:\n1 бр. LDNIO LS901 USB-A към Micro USB кабел 1 m",
            ],
            [
                'name' => 'USB Кабел 4в1 USB-A / C към USB-C / Lightning HOCO 240W U151 1.2 м',
                'slug' => 'usb-kabel-4v1-usb-a-c-kam-usb-c-lightning-hoco-240w-u151',
                'subtitle' => null,
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/usb-kabel-4v1-usb-a-c-kam-usb-c-lightning-hoco-240w-u151.jpg',
                'price_in_stotinki' => 2995,
                'compare_at_price_in_stotinki' => 5990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "USB Кабел 4в1 USB-A/C към USB-C/Lightning, HOCO 240W U151 1.2 м\nУниверсален кабел 4 в 1, който позволява зареждане на различни устройства с един кабел, благодарение на комбинацията от USB-A и USB-C към USB-C и Lightning.\nПодходящ за използване с лаптопи, смартфони и Apple устройства.\nКабелът поддържа висока мощност до 240W (USB-C към USB-C), както и бързо зареждане при останалите конфигурации – до 100W (USB-A към USB-C) и до 30W PD за Lightning.\nТова го прави практично решение за ежедневна употреба у дома, в офиса или при пътуване.\nИзработен е от здрава плетена обвивка с подсилени конектори от цинкова сплав, което осигурява устойчивост на огъване, износване и дълъг живот.\nДебелият кабел гарантира стабилна работа и безопасно зареждане.",
            ],
        ];
    }

    /**
     * Преходници и адаптери.
     *
     * @return list<array<string, mixed>>
     */
    protected function adapters(): array
    {
        return [
            [
                'name' => 'AUX кабел XO NB-R175A',
                'slug' => 'aux-kabel-xo-nb-r175a',
                'subtitle' => '3.5 мм · 1 м · черен',
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/aux-kabel-xo-nb-r175a.jpg',
                'price_in_stotinki' => 1845,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Качествен AUX кабел XO-NB-R175A - 3.5мм жак (мъжки) към 3.5мм жак (мъжки) за свързване на устройства с жак за слушалки към стерео системи, усилватели, високоговорители и др.\nБранд: XO\nВид продукт: Кабел\nДължина на кабела: 1 м\nМатериал: Метал, Пластмаса\nСъвместим модел: Универсален\nСъвместима марка: Универсален\nТип кабел: 3.5 мм жак - 3.5 мм жак\nЦвят: Черен",
            ],
            [
                'name' => 'Bluetooth приемник Hoco E53',
                'slug' => 'bluetooth-priemnik-hoco-e53',
                'subtitle' => 'AUX 3.5 мм',
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/bluetooth-priemnik-hoco-e53.jpg',
                'price_in_stotinki' => 3895,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Безжичен Bluetooth аудио приемник, предназначен за използване в автомобил чрез AUX вход\nУстройството позволява да предавате музика безжично от вашия смартфон към аудиосистемата на колата и да провеждате разговори със свободни ръце (hands-free).\nКапацитет от 145 mAh, който осигурява до 10 часа време за музика или разговори.\nПълното зареждане отнема около 2 часа чрез type C -USB порт.",
            ],
            [
                'name' => 'AUX Кабел Phone Planet PP-AUXC-120-BK USB Type-C към 3.5 мм',
                'slug' => 'aux-kabel-phone-planet-pp-auxc-120-bk-usb-type-c-kam-3-5',
                'subtitle' => null,
                'brand' => 'phone-planet',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/aux-kabel-phone-planet-pp-auxc-120-bk-usb-type-c-kam-3-5.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => true,
                'is_featured' => true,
                'description' => "AUX Кабел Phone Planet PP-AUXC-120-BK USB Type-C към 3.5 мм\n- Кабелът Phone Planet PP-AUXC-120-BK е USB Type-C към 3.5 мм аудио AUX кабел с дължина 1.2 метра, предназначен за свързване на устройства с USB-C порт към аудио системи с 3.5 мм вход.\n- Той е подходящ за използване с автомобили, слушалки, високоговорители и други аудио устройства.",
            ],
            [
                'name' => 'AUX Кабел Maxlife мъжко 3.5 мм - мъжко 3.5 мм 1м',
                'slug' => 'aux-kabel-maxlife-mazhko-3-5-mm-mazhko-3-5-mm-1m-cheren',
                'subtitle' => 'Черен',
                'brand' => 'maxlife',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/aux-kabel-maxlife-mazhko-3-5-mm-mazhko-3-5-mm-1m-cheren.jpg',
                'price_in_stotinki' => 1945,
                'compare_at_price_in_stotinki' => 3890,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Кабел AUX Maxlife мъжко 3.5 мм - мъжко 3.5 мм 1м - Черен\n- Аудио кабел Maxlife мъжко 3.5 мм - мъжко 3.5 мм с дължина 1 м е надежден инструмент за свързване на различни аудиоустройства, като телефони, таблети и много други, към домашно кино, радио за кола или друго аудиооборудване, оборудвано с жак конектор.\n- С дължина от 1 м той дава пълна гъвкавост при разполагането на аудио устройствата.\n- Отличава се с най-високо качество на изработката, което се изразява в отлично качество на звука и издръжливост.\n- Изработен от солидни материали, той е устойчив на механични повреди.\n- Кабелът осигурява отличен звук в аналогов стерео формат, без да се притеснявате от загуба на качество или смущения.\n- Той е не само практично, но и надеждно решение за свързване на различни аудиоустройства у дома или в автомобила.",
            ],
            [
                'name' => 'Безжичен Bluetooth приемник HOCO E80 / AUX Jack 3,5mm Bluetooth',
                'slug' => 'bezzhichen-bluetooth-priemnik-hoco-e80-aux-jack-3-5mm',
                'subtitle' => null,
                'brand' => 'hoco',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/bezzhichen-bluetooth-priemnik-hoco-e80-aux-jack-3-5mm.jpg',
                'price_in_stotinki' => 3895,
                'compare_at_price_in_stotinki' => 7790,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Безжичен Bluetooth приемник HOCO E80 / AUX Jack 3,5mm Bluetooth\n- Hoco E80 Travel – безжичен Bluetooth приемник за автомобил, предназначен да добави Bluetooth функционалност към аудио системи, които разполагат само с AUX вход (3,5 мм жак)\nУстройството е компактно, разполага с цифров LED дисплей, който показва нивото на батерията, и предлага до 12 часа работа с едно зареждане.",
            ],
        ];
    }

    /**
     * Стойки за кола.
     *
     * @return list<array<string, mixed>>
     */
    protected function carMounts(): array
    {
        return [
            [
                'name' => 'Стойка за кола за Xiaomi Redmi Note 15 Pro Plus',
                'slug' => 'stoyka-za-kola-za-xiaomi-redmi-note-15-pro-plus',
                'subtitle' => null,
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => null,
                'image_path' => 'images/products/stoyka-za-kola-za-xiaomi-redmi-note-15-pro-plus.jpg',
                'price_in_stotinki' => 2745,
                'compare_at_price_in_stotinki' => 5490,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Стойка за кола за Xiaomi Redmi Note 15 Pro Plus\nСъс стойката за кола можете лесно да монтирате телефона си на предното стъкло на колата.\n- Първокласната и универсална стойка държи здраво телефона ви, така че да имате ясен изглед върху екрана му.\n- Тази стойка е съвместима с телефони на всички основни марки като Apple, Samsung, Huawei, Xiaomi, Honor, Motorola, Realme и е отлично решение за поставяне на телефона във вашата кола.\n- Благодарение на регулируемата си фиксираща скоба, стойката може да държи здраво телефона ви дори с поставен калъф.\n- Тя е изключително удобна, тъй като ви предлага напълно регулируем ъгъл на гледане и може да се върти на 360 градуса.\n- Освен това всички бутони и конектори на телефона са лесно достъпни, така че имате ясна видимост и достъп до екрана.\n- Тази стойка има издръжлив дизайн и разполага със здрава вендуза за лесен и безопасен монтаж.\n- Захваща телефона в",
            ],
            [
                'name' => 'Стойка за кола за Xiaomi 17T Pro',
                'slug' => 'stoyka-za-kola-za-xiaomi-17t-pro',
                'subtitle' => null,
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => null,
                'image_path' => 'images/products/stoyka-za-kola-za-xiaomi-17t-pro.jpg',
                'price_in_stotinki' => 2745,
                'compare_at_price_in_stotinki' => 5490,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Със стойката за кола можете лесно да монтирате телефона си на предното стъкло на колата.\n- Първокласната и универсална стойка държи здраво телефона ви, така че да имате ясен изглед върху екрана му.\n- Тази стойка е съвместима с телефони на всички основни марки като Apple, Samsung, Huawei, Xiaomi, Honor, Motorola, Realme и е отлично решение за поставяне на телефона във вашата кола.\n- Благодарение на регулируемата си фиксираща скоба, стойката може да държи здраво телефона ви дори с поставен калъф.\n- Тя е изключително удобна, тъй като ви предлага напълно регулируем ъгъл на гледане и може да се върти на 360 градуса.\n- Освен това всички бутони и конектори на телефона са лесно достъпни, така че имате ясна видимост и достъп до екрана.\n- Тази стойка има издръжлив дизайн и разполага със здрава вендуза за лесен и безопасен монтаж.\n- Захваща телефона ви сигурно, като същевременно го предпазва от над",
            ],
            [
                'name' => 'Стойка за кола за Xiaomi 17T',
                'slug' => 'stoyka-za-kola-za-xiaomi-17t',
                'subtitle' => null,
                'brand' => null,
                'device_family' => DeviceFamily::Xiaomi,
                'case_type' => null,
                'image_path' => 'images/products/stoyka-za-kola-za-xiaomi-17t.jpg',
                'price_in_stotinki' => 2745,
                'compare_at_price_in_stotinki' => 5490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Със стойката за кола можете лесно да монтирате телефона си на предното стъкло на колата.\n- Първокласната и универсална стойка държи здраво телефона ви, така че да имате ясен изглед върху екрана му.\n- Тази стойка е съвместима с телефони на всички основни марки като Apple, Samsung, Huawei, Xiaomi, Honor, Motorola, Realme и е отлично решение за поставяне на телефона във вашата кола.\n- Благодарение на регулируемата си фиксираща скоба, стойката може да държи здраво телефона ви дори с поставен калъф.\n- Тя е изключително удобна, тъй като ви предлага напълно регулируем ъгъл на гледане и може да се върти на 360 градуса.\n- Освен това всички бутони и конектори на телефона са лесно достъпни, така че имате ясна видимост и достъп до екрана.\n- Тази стойка има издръжлив дизайн и разполага със здрава вендуза за лесен и безопасен монтаж.\n- Захваща телефона ви сигурно, като същевременно го предпазва от над",
            ],
            [
                'name' => 'Стойка за кола за Samsung Galaxy A27',
                'slug' => 'stoyka-za-kola-za-samsung-galaxy-a27',
                'subtitle' => null,
                'brand' => null,
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/stoyka-za-kola-za-samsung-galaxy-a27.jpg',
                'price_in_stotinki' => 2745,
                'compare_at_price_in_stotinki' => 5490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Със стойката за кола можете лесно да монтирате телефона си на предното стъкло на колата.\n- Първокласната и универсална стойка държи здраво телефона ви, така че да имате ясен изглед върху екрана му.\n- Тази стойка е съвместима с телефони на всички основни марки като Apple, Samsung, Huawei, Xiaomi, Honor, Motorola, Realme и е отлично решение за поставяне на телефона във вашата кола.\n- Благодарение на регулируемата си фиксираща скоба, стойката може да държи здраво телефона ви дори с поставен калъф.\n- Тя е изключително удобна, тъй като ви предлага напълно регулируем ъгъл на гледане и може да се върти на 360 градуса.\n- Освен това всички бутони и конектори на телефона са лесно достъпни, така че имате ясна видимост и достъп до екрана.\n- Тази стойка има издръжлив дизайн и разполага със здрава вендуза за лесен и безопасен монтаж.\n- Захваща телефона ви сигурно, като същевременно го предпазва от над",
            ],
            [
                'name' => 'Стойка за кола за Magic8 Lite / Honor Magic 8 Lite / Honor X9d / Honor X70',
                'slug' => 'stoyka-za-kola-za-magic8-lite-honor-magic-8-lite-honor',
                'subtitle' => null,
                'brand' => null,
                'device_family' => DeviceFamily::Honor,
                'case_type' => null,
                'image_path' => 'images/products/stoyka-za-kola-za-magic8-lite-honor-magic-8-lite-honor.jpg',
                'price_in_stotinki' => 2745,
                'compare_at_price_in_stotinki' => 5490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Стойка за кола за Magic8 Lite / Honor Magic 8 Lite / Honor X9d / Honor X70\nСъс стойката за кола можете лесно да монтирате телефона си на предното стъкло на колата.\n- Първокласната и универсална стойка държи здраво телефона ви, така че да имате ясен изглед върху екрана му.\n- Тази стойка е съвместима с телефони на всички основни марки като Apple, Samsung, Huawei, Xiaomi, Honor, Motorola, Realme и е отлично решение за поставяне на телефона във вашата кола.\n- Благодарение на регулируемата си фиксираща скоба, стойката може да държи здраво телефона ви дори с поставен калъф.\n- Тя е изключително удобна, тъй като ви предлага напълно регулируем ъгъл на гледане и може да се върти на 360 градуса.\n- Освен това всички бутони и конектори на телефона са лесно достъпни, така че имате ясна видимост и достъп до екрана.\n- Тази стойка има издръжлив дизайн и разполага със здрава вендуза за лесен и безопасен м",
            ],
        ];
    }

    /**
     * Калъфи за таблети.
     *
     * @return list<array<string, mixed>>
     */
    protected function tabletCases(): array
    {
        return [
            [
                'name' => 'Луксозен калъф Etteri за Apple iPad Pro (2024) 11 инча',
                'slug' => 'luksozen-kalaf-etteri-za-apple-ipad-pro-2024-11-incha',
                'subtitle' => 'черен',
                'brand' => 'etteri',
                'device_family' => null,
                'case_type' => CaseType::Hard,
                'image_path' => 'images/products/luksozen-kalaf-etteri-za-apple-ipad-pro-2024-11-incha.jpg',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => 11990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Стилен, тънък и изключително надежден, черният калъф Etteri за Apple iPad Pro (2024) 11 инча осигурява безкомпромисна 360-градусова защита за вашия премиум таблет.\nСъздаден с мисъл за съвременния потребител, този калъф съчетава елегантен бизнес дизайн с висок клас материали, които предпазват устройството от удари, драскотини и ежедневно износване, без да добавят излишно обем.\nПерфектно пасване: Проектиран ексклузивно за Apple iPad Pro (2024) 11\" с прецизни изрези за всички портове, бутони, високоговорители и камерата.\nСмарт функция за сън/събуждане: Магнитният капак автоматично активира екрана при отваряне и го заключва при затваряне, което спестява енергия и удължава живота на батерията.\nМултифункционална стойка: Сгъваемият дизайн тип \"оригами\" или \"триъгълник\" позволява позициониране в два удобни ъгъла – идеален за гледане на видео, видео разговори или ергономично писане.\nСпециално мяс",
            ],
            [
                'name' => 'Калъф тип тефтер за Samsung Galaxy Tab A9+ Plus 11.0"/ A11+ Plus 11.0" Tech-Protect SmartCase',
                'slug' => 'kalaf-tip-tefter-za-samsung-galaxy-tab-a9-plus-11-0-quot',
                'subtitle' => null,
                'brand' => 'tech-protect',
                'device_family' => null,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/kalaf-tip-tefter-za-samsung-galaxy-tab-a9-plus-11-0-quot.jpg',
                'price_in_stotinki' => 4495,
                'compare_at_price_in_stotinki' => 8990,
                'is_new' => false,
                'is_featured' => true,
                'description' => null,
            ],
            [
                'name' => 'Универсален кожен калъф със стойка за таблет 7\'\' / 8\'\'',
                'slug' => 'universalen-kozhen-kalaf-sas-stoyka-za-tablet-7-039-039-8',
                'subtitle' => 'розов еднорог',
                'brand' => null,
                'device_family' => null,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/universalen-kozhen-kalaf-sas-stoyka-za-tablet-7-039-039-8.jpg',
                'price_in_stotinki' => 2845,
                'compare_at_price_in_stotinki' => 5690,
                'is_new' => false,
                'is_featured' => false,
                'description' => null,
            ],
            [
                'name' => 'Универсален кожен калъф със стойка Fantasia за таблет 7\'\' / 8\'\'',
                'slug' => 'universalen-kozhen-kalaf-sas-stoyka-fantasia-za-tablet-7',
                'subtitle' => 'Don`t Touch',
                'brand' => null,
                'device_family' => null,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/universalen-kozhen-kalaf-sas-stoyka-fantasia-za-tablet-7.jpg',
                'price_in_stotinki' => 2845,
                'compare_at_price_in_stotinki' => 5690,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Универсален кожен калъф със стойка Fantasia за таблет 7'' / 8'' - Don`t Touch\nВисококачествен калъф за таблет тип тефтер. Придава елегантност на устройството, предпазвайки го от счупване или надраскване. Предимството на калъфа тип тефтер е, че предпазва както корпуса и гърба на таблета, така и неговия дисплей. Предлага се в голямо разнообразие от цветове и дизайни. Калъфът е изработен от изкуствена кожа, а таблетът се захваща здраво с щипки в четирите ъгъла. Това позволява стабилност и възможност за поставяне в изправено положение. Калъфът за таблет е изключително удобен и функционален. Осигурява достъп до всички бутони, входове и изходи на устройството. Подходящ е за таблети с размер: 7-8 инча",
            ],
            [
                'name' => 'Универсален кожен калъф със стойка за таблет 9\'\' / 10\'\'',
                'slug' => 'universalen-kozhen-kalaf-sas-stoyka-za-tablet-9-039-039',
                'subtitle' => 'Многоцветен Шарен еднорог',
                'brand' => null,
                'device_family' => null,
                'case_type' => CaseType::Leather,
                'image_path' => 'images/products/universalen-kozhen-kalaf-sas-stoyka-za-tablet-9-039-039.jpg',
                'price_in_stotinki' => 2845,
                'compare_at_price_in_stotinki' => 5690,
                'is_new' => false,
                'is_featured' => false,
                'description' => null,
            ],
        ];
    }

    /**
     * Универсални калъфи.
     *
     * @return list<array<string, mixed>>
     */
    protected function universalCases(): array
    {
        return [
            [
                'name' => 'Мултифункционална спортна чанта за телефон 2в1',
                'slug' => 'multifunktsionalna-sportna-chanta-za-telefon-2v1-cherna',
                'subtitle' => 'черна',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/multifunktsionalna-sportna-chanta-za-telefon-2v1-cherna.jpg',
                'price_in_stotinki' => 2945,
                'compare_at_price_in_stotinki' => 5890,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Мултифункционална спортна чанта за телефон 2в1 - черна\nПрактична и стилна спортна чанта 2в1, подходяща за ежедневието, разходки, спорт и пътуване.\nИзработена е от здрав материал Oxford 900D с водоотблъскващо PU покритие, което предпазва съдържанието от влага и замърсяване.\nМоже да се носи като чанта през рамо или като компактно калъфче благодарение на регулируемата презрамка.\nПобира смартфони до 7 инча, както и ключове, карти, слушалки и други дребни принадлежности.\nЧерният цвят и минималистичният дизайн я правят лесна за съчетаване с всякакъв стил.",
            ],
            [
                'name' => 'Универсален водоустойчив калъф Waterproof Case за мобилен телефон 6.5-6.8" инча',
                'slug' => 'universalen-vodoustoychiv-kalaf-waterproof-case-za',
                'subtitle' => 'черен',
                'brand' => null,
                'device_family' => null,
                'case_type' => CaseType::Shockproof,
                'image_path' => 'images/products/universalen-vodoustoychiv-kalaf-waterproof-case-za.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Универсален водоустойчив калъф Waterproof Case за мобилен телефон 6.5-6.8\" инча - черен\n- Универсален водоустойчив калъф, подходящ за телефон, фотоапарат или друго мобилно устройство.\n- Ефективно предпазва от водни пръски и пясък.\n- Устройството ти ще бъде непокътнато, тъй като калъфът е изработен от висококачествена непромокаема пластмаса.\n- Благодарение на него ще снимаш спокойно и ще запечаташ любимите спомени от лятото, без да се притесняваш за твоето устройство.\n- Избери сигурна и надеждна защита за него по време на морската си ваканция.",
            ],
            [
                'name' => 'Универсален калъф / джоб / с връзка за врат',
                'slug' => 'universalen-kalaf-dzhob-s-vrazka-za-vrat-zelen-kamuflazh',
                'subtitle' => 'зелен камуфлаж',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/universalen-kalaf-dzhob-s-vrazka-za-vrat-zelen-kamuflazh.jpg',
                'price_in_stotinki' => 2245,
                'compare_at_price_in_stotinki' => 4490,
                'is_new' => false,
                'is_featured' => false,
                'description' => null,
            ],
        ];
    }

    /**
     * Handsfree.
     *
     * @return list<array<string, mixed>>
     */
    protected function wiredHeadsets(): array
    {
        return [
            [
                'name' => 'Оригинални стерео слушалки AKG / handsfree / за Samsung Galaxy S25 FE Type-C',
                'slug' => 'originalni-stereo-slushalki-akg-handsfree-za-samsung',
                'subtitle' => 'черни',
                'brand' => 'akg',
                'device_family' => DeviceFamily::Samsung,
                'case_type' => null,
                'image_path' => 'images/products/originalni-stereo-slushalki-akg-handsfree-za-samsung.jpg',
                'price_in_stotinki' => 3695,
                'compare_at_price_in_stotinki' => 7390,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Оригинални стерео слушалки AKG / handsfree / за Samsung Galaxy S25 FE Type-C - черни\nСлушането на музика у дома, по време на почивка на работа или учене, или по време на пътуване може да бъде истинско удоволствие, при условие че се извършва с качествена техника. Ако цените чистотата на звука и комфорта при слушане, със сигурност ще оцените слушалките Samsung за поставяне в ушите с USB Type C кабел. Пригодени са за телефони, които нямат вграден 3.5mm mini жак конектор. Перфектен за слушане на музика и провеждане на разговори, без да се налага да държите телефона в ръка.\nТип слушалки: за поставяне в ушите, жични\nЧувствителност: 94.3dB ± 3dB (-10dBFS вход)\nЕргономичният дизайн отразява прецизно геометрията на ухото и по този начин прави слушалките да пасват перфектно. Дори след няколко часа употреба, можете да се насладите напълно на любимата си музика без възпалени и уморени уши.\nСлушалкит",
            ],
            [
                'name' => 'Слушалки HF XO EP74 USB-C',
                'slug' => 'slushalki-hf-xo-ep74-usb-c-cherni',
                'subtitle' => 'Черни',
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/slushalki-hf-xo-ep74-usb-c-cherni.jpg',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => true,
                'description' => "- Слушалки XO EP74 са с USB-C кабел и разполагат с микрофон.\n- Кабелът, изработен от гъвкав силикон, ще осигури възможно най-дългата употреба.",
            ],
            [
                'name' => 'Стерео слушалки XO EP66 / handsfree / Type C',
                'slug' => 'stereo-slushalki-xo-ep66-handsfree-type-c',
                'subtitle' => null,
                'brand' => 'xo',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/stereo-slushalki-xo-ep66-handsfree-type-c.png',
                'price_in_stotinki' => 2445,
                'compare_at_price_in_stotinki' => 4890,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Стерео слушалки XO EP66 / handsfree / Type C\n- XO EP66 са кабелни слушалки за смартфони, оборудвани с USB-C гнездо и без 3,5 мм жак.\n- Кабелът, изработен от гъвкав силикон, ще осигури възможно най-дългото време за използване.",
            ],
        ];
    }

    /**
     * Тонколони.
     *
     * @return list<array<string, mixed>>
     */
    protected function speakers(): array
    {
        return [
            [
                'name' => 'Преносима Bluetooth Колонка Borofone BR1 10W',
                'slug' => 'prenosima-bluetooth-kolonka-borofone-br1-10w-cherna',
                'subtitle' => 'Черна',
                'brand' => 'borofone',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/prenosima-bluetooth-kolonka-borofone-br1-10w-cherna.png',
                'price_in_stotinki' => 4495,
                'compare_at_price_in_stotinki' => 8990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Преносима Bluetooth Колонка Borofone BR1 10W - Черна\nМощна Bluetooth колонка с дълбок бас и качествен звук.\nПодходяща за музика на открито и закрито.\nНасладете се на висококачествен звук с Borofone BR1 Beyond портативна Bluetooth колонка в елегантен черен цвят.\nТази безжична колонка е идеалният спътник за музика у дома, на път или по време на спорт.\nСнабдена с Bluetooth V5.0, тя осигурява стабилна и бърза връзка с всичките ви устройства.\nМощността от 10W (2x5W) гарантира качествен звук и дълбок бас.\nВградената 1200mAh батерия предлага до 3 часа време на работа, а пълното зареждане отнема приблизително 3 часа.\nРазполага с функции като HandsFree с вграден микрофон, поддръжка на microSD карта и FM радио, както и AUX вход за жична връзка.\nКомпактният дизайн (180 x 65 x 66.2 мм) и лекото тегло от 352 гр правят Borofone BR1 изключително удобна за пренасяне.\nВреме на работа: До 3 часа (възпроиз",
            ],
            [
                'name' => 'Преносима Безжична Колонка Borofone BR4 Horizon / Borofone Portable Bluetooth Speaker BR4 Horizon',
                'slug' => 'prenosima-bezzhichna-kolonka-borofone-br4-horizon',
                'subtitle' => 'Черна',
                'brand' => 'borofone',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/prenosima-bezzhichna-kolonka-borofone-br4-horizon.png',
                'price_in_stotinki' => 4495,
                'compare_at_price_in_stotinki' => 8990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Преносима Безжична Колонка Borofone BR4 Horizon / Borofone Portable Bluetooth Speaker BR4 Horizon - Черна\nОткрийте свободата на музиката с портативната тонколона BOROFONE BR4 Horizon – вашият идеален спътник за всяко приключение!\nТази компактна безжична тонколона в елегантен черен цвят осигурява кристално чист и мощен звук благодарение на 52мм говорител и двойна диафрагма.\nТя е проектирана да бъде лека (308гр) и с удобни малки размери (142 x 83 x 86 мм), което я прави перфектна спортна тонколона за пътуване, тренировки или отдих на открито.\nОборудвана с най-новата технология Bluetooth V5.0 JL, BOROFONE BR4 Horizon гарантира стабилна и бърза безжична връзка с всички ваши устройства, поддържайки A2DP, AVRCP и HFP протоколи за висококачествено аудио преживяване.\nС вградена батерия от 500mAh, тя предлага до 2 часа възпроизвеждане на музика или разговори (при 80% сила на звука) и се зарежда и",
            ],
        ];
    }

    /**
     * Оригинални батерии.
     *
     * @return list<array<string, mixed>>
     */
    protected function replacementBatteries(): array
    {
        return [
            [
                'name' => 'Оригинална батерия HB366481ECW за Huawei Honor 8 Lite',
                'slug' => 'originalna-bateriya-hb366481ecw-za-huawei-honor-8-lite',
                'subtitle' => '3000mAh',
                'brand' => 'huawei',
                'device_family' => DeviceFamily::Huawei,
                'case_type' => null,
                'image_path' => 'images/products/originalna-bateriya-hb366481ecw-za-huawei-honor-8-lite.jpg',
                'price_in_stotinki' => 4445,
                'compare_at_price_in_stotinki' => 8890,
                'is_new' => true,
                'is_featured' => true,
                'description' => 'Оригинална батерия HB366481ECW за Huawei Honor 8 Lite - 3000mAh',
            ],
            [
                'name' => 'Оригинална батерия HB366481ECW за Huawei P20 Lite',
                'slug' => 'originalna-bateriya-hb366481ecw-za-huawei-p20-lite-3000mah',
                'subtitle' => '3000mAh',
                'brand' => 'huawei',
                'device_family' => DeviceFamily::Huawei,
                'case_type' => null,
                'image_path' => 'images/products/originalna-bateriya-hb366481ecw-za-huawei-p20-lite-3000mah.jpg',
                'price_in_stotinki' => 4445,
                'compare_at_price_in_stotinki' => 8890,
                'is_new' => false,
                'is_featured' => true,
                'description' => null,
            ],
            [
                'name' => 'Оригинална батерия HB366481ECW за Huawei P10 Lite',
                'slug' => 'originalna-bateriya-hb366481ecw-za-huawei-p10-lite-3000mah',
                'subtitle' => '3000mAh',
                'brand' => 'huawei',
                'device_family' => DeviceFamily::Huawei,
                'case_type' => null,
                'image_path' => 'images/products/originalna-bateriya-hb366481ecw-za-huawei-p10-lite-3000mah.jpg',
                'price_in_stotinki' => 4445,
                'compare_at_price_in_stotinki' => 8890,
                'is_new' => false,
                'is_featured' => false,
                'description' => null,
            ],
            [
                'name' => 'Оригинална батерия HB405979ECW за Huawei Nova Smart',
                'slug' => 'originalna-bateriya-hb405979ecw-za-huawei-nova-smart',
                'subtitle' => '2920mAh',
                'brand' => 'huawei',
                'device_family' => DeviceFamily::Huawei,
                'case_type' => null,
                'image_path' => 'images/products/originalna-bateriya-hb405979ecw-za-huawei-nova-smart.jpg',
                'price_in_stotinki' => 4445,
                'compare_at_price_in_stotinki' => 8890,
                'is_new' => false,
                'is_featured' => false,
                'description' => 'Оригинална батерия HB405979ECW за Huawei Nova Smart - 2920mAh',
            ],
            [
                'name' => 'Оригинална батерия HB405979ECW за Huawei Y5 2019',
                'slug' => 'originalna-bateriya-hb405979ecw-za-huawei-y5-2019-2920mah',
                'subtitle' => '2920mAh',
                'brand' => 'huawei',
                'device_family' => DeviceFamily::Huawei,
                'case_type' => null,
                'image_path' => 'images/products/originalna-bateriya-hb405979ecw-za-huawei-y5-2019-2920mah.jpg',
                'price_in_stotinki' => 4445,
                'compare_at_price_in_stotinki' => 8890,
                'is_new' => false,
                'is_featured' => false,
                'description' => null,
            ],
        ];
    }

    /**
     * Карти памет.
     *
     * @return list<array<string, mixed>>
     */
    protected function memoryCards(): array
    {
        return [
            [
                'name' => 'Карта памет Micro SDHC Card SanDisk 16GB Class 10',
                'slug' => 'karta-pamet-micro-sdhc-card-sandisk-16gb-class-10',
                'subtitle' => null,
                'brand' => 'sandisk',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/karta-pamet-micro-sdhc-card-sandisk-16gb-class-10.jpg',
                'price_in_stotinki' => 1700,
                'compare_at_price_in_stotinki' => 3400,
                'is_new' => true,
                'is_featured' => true,
                'description' => null,
            ],
            [
                'name' => 'Карта памет Micro SDHC Card GOODRAM 32GB + Micro SD Adapter UHS1 Class 10',
                'slug' => 'karta-pamet-micro-sdhc-card-goodram-32gb-micro-sd-adapter',
                'subtitle' => null,
                'brand' => 'goodram',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/karta-pamet-micro-sdhc-card-goodram-32gb-micro-sd-adapter.jpg',
                'price_in_stotinki' => 2250,
                'compare_at_price_in_stotinki' => 4500,
                'is_new' => false,
                'is_featured' => true,
                'description' => 'Карта памет Micro SDHC Card GOODRAM 32GB + Micro SD Adapter UHS1 Class 10',
            ],
            [
                'name' => 'Карта памет SDHD Card TOSHIBA High Speed Standard 16GB Class 4',
                'slug' => 'karta-pamet-sdhd-card-toshiba-high-speed-standard-16gb',
                'subtitle' => null,
                'brand' => 'toshiba',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/karta-pamet-sdhd-card-toshiba-high-speed-standard-16gb.jpg',
                'price_in_stotinki' => 1645,
                'compare_at_price_in_stotinki' => 3290,
                'is_new' => false,
                'is_featured' => false,
                'description' => 'Карта памет SDHD Card TOSHIBA High Speed Standard 16GB Class 4',
            ],
            [
                'name' => 'Карта памет Micro SD card Toshiba High Speed 16GB + Micro SD Adapter',
                'slug' => 'karta-pamet-micro-sd-card-toshiba-high-speed-16gb-micro',
                'subtitle' => null,
                'brand' => 'toshiba',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/karta-pamet-micro-sd-card-toshiba-high-speed-16gb-micro.jpg',
                'price_in_stotinki' => 1745,
                'compare_at_price_in_stotinki' => 3490,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Micro SD card Class 4 Toshiba High Speed - 16GB + Micro SD Adapter\n- Многофункционална карта памет, подходяща за употреба с дигитални фотоапарати, PDA - устройства, mp3 плеъри, мобилни телефони и др. устройства, разполагащи с SD/Micro SD слот - Адаптер за SD слот в комплекта",
            ],
            [
                'name' => 'MicroSDHC карта / 16GB / SANDISK CLASS 4',
                'slug' => 'microsdhc-karta-16gb-sandisk-class-4',
                'subtitle' => null,
                'brand' => 'sandisk',
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/microsdhc-karta-16gb-sandisk-class-4.jpg',
                'price_in_stotinki' => 1750,
                'compare_at_price_in_stotinki' => 3500,
                'is_new' => false,
                'is_featured' => false,
                'description' => null,
            ],
        ];
    }

    /**
     * Коли.
     *
     * @return list<array<string, mixed>>
     */
    protected function toyCars(): array
    {
        return [
            [
                'name' => 'Метална кола BMW Z4 M40i',
                'slug' => 'metalna-kola-bmw-z4-m40i',
                'subtitle' => 'Мащаб 1:24 · светлини и звуци',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/metalna-kola-bmw-z4-m40i.jpg',
                'price_in_stotinki' => 6945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Метална количка BMW Z4 M40i Motorsport - 1:24\nПотопете се в света на високите скорости с детайлния умален модел на легендарния спортен автомобил BMW Z4 M40i Motorsport. Тази висококачествена метална количка в мащаб 1:24 съчетава автентичен дизайн, здравина и интерактивни функции, които ще пленят както децата, така и запалените колекционери.",
            ],
            [
                'name' => 'Метална кола BMW M4 Coupe',
                'slug' => 'metalna-kola-bmw-m4-coupe',
                'subtitle' => 'Мащаб 1:24 · светлини и звуци',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/metalna-kola-bmw-m4-coupe.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => 'Усетете тръпката от баварската мощ с това прецизно копие на BMW M4 Coupe. Този модел в мащаб 1:24 не е просто играчка, а детайлно произведение на изкуството, което пренася духа на "M Power" директно на вашия рафт или бюро.',
            ],
            [
                'name' => 'Метална кола Audi RS6 Avant',
                'slug' => 'metalna-kola-audi-rs6-avant',
                'subtitle' => 'Мащаб 1:32 · камуфлажна',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/metalna-kola-audi-rs6-avant.jpg',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => 9990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Метална количка Audi RS6 Avant (C8) - Мащаб 1:32, Дизайн „Камуфлаж“\nПревърнете страстта към високите скорости в реалност с този детайлен модел на легендарното Audi RS6 Avant. Съчетание от агресивен дизайн и безкомпромисно качество, тази количка в уникален камуфлажен десен е задължителна за всеки колекционер и малък фен на автомобилите.\nМащаб: 1:32 - перфектен баланс между размер и детайлност.",
            ],
            [
                'name' => 'Метална кола Nissan Skyline GT-R',
                'slug' => 'metalna-kola-nissan-skyline-gtr',
                'subtitle' => 'Мащаб 1:24 · светлини и звуци',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/metalna-kola-nissan-skyline-gtr.jpeg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => 'Притежавайте легендата "Godzilla" - Nissan Skyline GT-R R34 в мащаб 1:24',
            ],
            [
                'name' => 'Метална кола Mitsubishi Lancer Evolution',
                'slug' => 'metalna-kola-mitsubishi-lancer-evo',
                'subtitle' => 'Мащаб 1:24 · светлини и звуци',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/metalna-kola-mitsubishi-lancer-evo.jpg',
                'price_in_stotinki' => 8995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Метална количка Mitsubishi Lancer Evolution - Легендата на пътя\nДобавете мощ и стил към своята колекция с този изключително детайлен метален модел на Mitsubishi Lancer Evolution.\nИзработена в мащаб 1:24, тази количка улавя агресивния дух и емблематичния дизайн на японската рали легенда.\nПерфектен избор както за запалени колекционери, така и за подарък на малки и големи фенове на високите скорости!",
            ],
            [
                'name' => 'Метална кола McLaren MCL60 Formula 1 2023 Australian Grand Prix',
                'slug' => 'metalna-kola-mclaren-mcl60-formula-1-2023-australian',
                'subtitle' => '1:24',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/metalna-kola-mclaren-mcl60-formula-1-2023-australian.png',
                'price_in_stotinki' => 13995,
                'compare_at_price_in_stotinki' => 27990,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Метална кола McLaren MCL60 Formula 1 2023 Australian Grand Prix - 1:24\nСкорост и съвършенство: Болидът на McLaren F1 Team във вашата колекция\nПочувствайте духа на Формула 1 с този прецизно изработен метален модел на McLaren F1 Team в мащаб 1:24.\nПроектиран за истински ценители на моторните спортове, този болид съчетава аеродинамичен дизайн и върхово качество на материалите.\nСъс своите внушителни размери и висока степен на симулация на интериора и екстериора, той е идеалният начин да се докоснете до технологичното съвършенство на една от най-успешните състезателни марки в историята.\nПремиум Die-Cast изработка – Корпусът е изработен от висококачествена метална сплав (alloy), допълнен с детайли от екологична ABS пластмаса.\nИнтерактивни ефекти – Оборудван със звукови и светлинни функции, които вдъхват живот на модела и пресъздават атмосферата на пистата.\nРеалистични гуми – Колелата са с авте",
            ],
        ];
    }

    /**
     * Занимателни.
     *
     * @return list<array<string, mixed>>
     */
    protected function activityToys(): array
    {
        return [
            [
                'name' => 'Конструктор Lamborghini Racing',
                'slug' => 'konstruktor-lamborghini-racing-rc',
                'subtitle' => '366 части · с дистанционно',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/konstruktor-lamborghini-racing-rc.png',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Комплектът съдържа:\n- 366 елемента на конструктор за сглобяване на кола,\n- дистанционно управление.\nРазмери на колата - 24.7 * 12.6 * 7.2 см.\nЗаредете колата с 3 батерии от 1.5 V size AA.\nЗаредете дистанционното с 2 батерии от 1.5 V size AA.\nЗа деца над 6 години.",
            ],
            [
                'name' => 'Радиоуправляем хеликоптер',
                'slug' => 'helikopter-radioupravlyaem-metalen',
                'subtitle' => 'Метален корпус',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/helikopter-radioupravlyaem-metalen.jpg',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Комплектът включва:\n- метален хеликоптер,\n- дистанционно управление,\n- USB кабел за зареждане на батерията.\nФункции:\n- светлина под корпуса,\n- 3D полет - на 360 °.\nХеликоптерът е изработен от специални удароустойчиви материали.\nПерките са разположени на два реда, за да може да развива по - голяма скорост.\nВреме за полет: 5 - 7 минути.\nОбхват на дистанционното - около 10 метра.",
            ],
            [
                'name' => 'Камион за боклук',
                'slug' => 'kamion-za-bokluk',
                'subtitle' => 'Звукови и светлинни ефекти',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/kamion-za-bokluk.jpg',
                'price_in_stotinki' => 3995,
                'compare_at_price_in_stotinki' => 7990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Комплектът съдържа:\n- камион,\n- 2 контейнера за боклук.\nФункции на камиона:\n- инерционен,\n- специализирани сигнални светлини,\n- специализирани реалистични звуци и сирени,\n- можете да сортирате боклука разделно - в отделни контейнери,\n- бутони за пневматично повдигане и сваляне на контейнера - посредством въздушно налягане,\n- мащаб - 1:16,\n- размери - 27.5 * 10.5 * 12.5 см.\n- цвят - оранжев, зелен и син\nКамионът е зареден с необходимите батерии.",
            ],
            [
                'name' => 'Мотокар с дистанционно управление и пушек RC 1:24',
                'slug' => 'motokar-s-distantsionno-upravlenie-i-pushek-rc-1-24',
                'subtitle' => null,
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/motokar-s-distantsionno-upravlenie-i-pushek-rc-1-24.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => true,
                'is_featured' => true,
                'description' => null,
            ],
            [
                'name' => 'Конструктор с 386 части кола с дистанционно управление',
                'slug' => 'konstruktor-s-386-chasti-kola-s-distantsionno-upravlenie',
                'subtitle' => 'Porsche 911 Racing',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/konstruktor-s-386-chasti-kola-s-distantsionno-upravlenie.png',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => 11990,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Конструктор с 386 части кола с дистанционно управление - Porsche 911 Racing\n- 386 елемента на конструктор за сглобяване на кола,\nЗаредете колата с 3 батерии от 1.5 V size AA.\nЗаредете дистанционното с 2 батерии от 1.5 V size AA.",
            ],
            [
                'name' => 'Ходещо плюшено куче пудел кафяво с аксесоари',
                'slug' => 'hodeshto-plyusheno-kuche-pudel-kafyavo-s-aksesoari',
                'subtitle' => null,
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/hodeshto-plyusheno-kuche-pudel-kafyavo-s-aksesoari.jpg',
                'price_in_stotinki' => 8950,
                'compare_at_price_in_stotinki' => 17900,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Ходещо плюшено куче пудел кафяво с аксесоари\nХодещо плюшено куче пудел в кафяв цвят е очарователна и интерактивна играчка, която ще зарадва всяко дете. Тази плюшена играчка може да ходи самостоятелно, когато се активира и има звукови ефекти като лай, които я правят още по-реалистична и забавна.",
            ],
        ];
    }

    /**
     * Колекционерски фигурки.
     *
     * @return list<array<string, mixed>>
     */
    protected function collectibleFigures(): array
    {
        return [
            [
                'name' => 'Фигура Son Goku',
                'slug' => 'figura-son-goku-32cm',
                'subtitle' => 'Dragon Ball · 32 см',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/figura-son-goku-32cm.png',
                'price_in_stotinki' => 8995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Вдъхнете живот на любимата си аниме поредица с тази впечатляваща фигура на Son Goku . Моделът се отличава с изключително детайлна изработка, която улавя силата и динамичната енергия на легендарния Сайян. Тя е перфектен акцент за всяка гейминг стая, офис или колекционерска витрина.\nТази внушителна фигура на Son Goku пренася екшъна директно у дома. Динамичната поза улавя духа на легендарния боец. Всеки мускул и гънка са пресъздадени с точност.",
            ],
            [
                'name' => 'Фигура Yoriichi Tsugikuni',
                'slug' => 'figura-yoriichi-tsugikuni-30cm',
                'subtitle' => 'Demon Slayer · 30 см',
                'brand' => null,
                'device_family' => null,
                'case_type' => null,
                'image_path' => 'images/products/figura-yoriichi-tsugikuni-30cm.png',
                'price_in_stotinki' => 8995,
                'compare_at_price_in_stotinki' => 17990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Пренесете легендарната сила от вселената на анимето Demon Slayer право във вашия дом с тази премиум, високо детайлна фигура на Йоричи Цугикуни.\nКато създател на първия дихателен стил (Слънчево дишане) и най-могъщия истребител на демони в историята, Йоричи е задължително попълнение за всеки истински фен и колекционер.\nСтатуетката се отличава с изключително ниво на прецизност при изработката.",
            ],
        ];
    }
}
