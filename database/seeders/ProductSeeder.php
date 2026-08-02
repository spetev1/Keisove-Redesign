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
     * shop's own product photography. The first eight of each category are the
     * ones the homepage carousels show; the rest are what they scroll to.
     */
    public function run(): void
    {
        $brands = Brand::pluck('id', 'slug');
        $categories = Category::get()->keyBy('slug');

        $catalogue = [
            'keisove' => $this->cases(),
            'parfyumi' => $this->perfumes(),
            'aksesoari' => $this->accessories(),
            'protektori' => $this->protectors(),
            'detski-igrachki' => $this->toys(),
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
     * @return list<array<string, mixed>>
     */
    protected function cases(): array
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
     * @return list<array<string, mixed>>
     */
    protected function perfumes(): array
    {
        return [
            [
                'name' => 'Lattafa Khamrah Dukhan',
                'slug' => 'lattafa-khamrah-dukhan-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
                'image_path' => 'images/products/lattafa-khamrah-dukhan-edp-100.jpg',
                'price_in_stotinki' => 7495,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => true,
                'description' => "Производител: Lattafa\nКоличество - 100мл.\nСъстав:\n- Връхни нотки - пименто, подправки, мандарина;\n- Средни нотки - лабданум, тамян, пачули, портокалов цвят;\n- Базови нотки - тютюн, пралина, зърна тонка, кехлибар, бензоин.",
            ],
            [
                'name' => 'Ombre Oud Intense Black',
                'slug' => 'ombre-oud-intense-black-edp-100',
                'subtitle' => 'Мъжки · EDP 100 мл.',
                'brand' => null,
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
                'image_path' => 'images/products/fragrance-world-proud-of-you-amber-edp-100.jpg',
                'price_in_stotinki' => 7945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Производител: Fragrance World\nFragrance World Proud of You Amber - топъл, елегантен аромат с пикантни нотки, златист кехлибар и дървесна мекота. Модерен и дълготраен.\nВдъхновен от Emporio Armani Stronger With You Amber / Giorgio Armani\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nАроматен профил:\nВръхни нотки:",
            ],
            [
                'name' => 'Asdaaf Ameerat Al Arab Prive Rose',
                'slug' => 'asdaaf-ameerat-al-arab-prive-rose-edp-100',
                'subtitle' => 'Дамски · EDP 100 мл.',
                'brand' => 'asdaaf',
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
                'image_path' => 'images/products/lattafa-ajwad-pink-to-pink-edp-60.png',
                'price_in_stotinki' => 4995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Вдъхновен от Oud Maracujá / Maison Crivelli\nПроизводител: Lattafa\nТип: Парфюмна вода\nКоличество: 60мл.\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nАроматни Нотки\n- Връхни нотки - розов грейпфрут, розов пипер, малина, гуава;\n- Средни нотки - роза, божур, магнолия;\n- Базови нотки - мускус, кожа, ванилия, амбра, мъх.",
            ],
            [
                'name' => 'Lattafa Victoria',
                'slug' => 'lattafa-victoria-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
                'image_path' => 'images/products/lattafa-victoria-edp-100.jpeg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Вдъхновен от Devotion / Dolce & Gabbana\nПроизводител: Lattafa\n- Lattafa Victoria е нежен гурме аромат с лимонов пай, елегантно нероли и топла ванилия.\n- Сладко-цитрусова изтънченост, напомняща на лек и кремообразен десерт.\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nLattafa Victoria\nАроматен профил:",
            ],
            [
                'name' => 'Lattafa Ana Abiyedh Coral',
                'slug' => 'lattafa-ana-abiyedh-coral-edp-60',
                'subtitle' => 'Дамски · EDP 60 мл.',
                'brand' => 'lattafa',
                'image_path' => 'images/products/lattafa-ana-abiyedh-coral-edp-60.png',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => true,
                'description' => "Производител: Lattafa\nВдъхновен от Wavechild / Room 1015\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\n- Тип - Парфюмна вода\n- Количество - 60мл.\nLattafa Ana Abiyedh Coral е завладяващ унисекс аромат, който излъчва топлина и елегантност.",
            ],
            [
                'name' => 'Lattafa Pride Al Qiam Gold',
                'slug' => 'lattafa-pride-al-qiam-gold-edp-100',
                'subtitle' => 'Унисекс · EDP 100 мл.',
                'brand' => 'lattafa',
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
                'image_path' => 'images/products/lattafa-ishq-al-shuyukh-gold-edp-100.jpg',
                'price_in_stotinki' => 6995,
                'compare_at_price_in_stotinki' => 13990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Вдъхновен от Rosendo Mateu Nº 5 Elixir / Rosendo Mateu Olfactive Expressions\n- Отличава се с изключителен интензитет и трайност, по добра от познатия оригинален парфюм.\n- И двата аромата споделят подобни нотки, което ги прави доста сходни по характер.\nПроизводител: Lattafa\nНаличност - В наличност\nАроматен профил и нотки\nIshq Al Shuyukh Gold е сложен и многопластов аромат, който се развива красиво върху кожата:",
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function accessories(): array
    {
        return [
            [
                'name' => 'Безжичен микрофон Hoco L21',
                'slug' => 'bezzhichen-mikrofon-hoco-l21',
                'subtitle' => 'USB-C с приемник',
                'brand' => 'hoco',
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
                'image_path' => 'images/products/slushalki-hoco-w35-90h.jpg',
                'price_in_stotinki' => 5995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => true,
                'is_featured' => false,
                'description' => "Hoco W35 са безжични слушалки с впечатляващ живот на батерията до 90 часа работа. Осигуряват стабилна Bluetooth връзка, удобни меки наушници и балансиран звук. Подходящи са за продължително слушане на музика, онлайн разговори и работа от разстояние.\nБранд: HOCO\nВерсия Bluetooth: 5.3\nВид продукт: Безжични слушалки\nМатериал: Метал, Пластмаса\nОбхват: 10 м\nСъвместим модел: Универсален\nСъвместима марка: Универсален\nТип: Over the ear\nТип свързване: Bluetooth",
            ],
            [
                'name' => 'Power Bank Dudao K28',
                'slug' => 'power-bank-dudao-k28-10000',
                'subtitle' => '10000 mAh · MagSafe · 22.5W',
                'brand' => 'dudao',
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
                'image_path' => 'images/products/power-bank-xo-pb313-20000.jpeg',
                'price_in_stotinki' => 6845,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Външната батерия XO PB313 20000 mAh осигурява надеждно преносимо захранване навсякъде.\nУстройството притежава оптимална изходяща мощност от 10W.\nМоделът разполага с два USB-A порта за едновременно зареждане.\nВграденият литиево-полимерен акумулатор (Li-Pol) гарантира изключително дълъг живот.\nОсновни предимства\nКапацитет от 20000 mAh.\nДва USB-A изходни порта.\nИнтелигентен Smart IC чип.\nЧетири LED индикатора за заряд.\nОгнеупорни ABS и PC материали.",
            ],
            [
                'name' => 'Смарт часовник Valdus VS14',
                'slug' => 'smart-watch-valdus-vs14',
                'subtitle' => 'Черен',
                'brand' => 'valdus',
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
                'image_path' => 'images/products/detski-led-chasovnik-stitch.jpg',
                'price_in_stotinki' => 4945,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "- Детският LED часовник със Стич е забавен и стилен аксесоар, перфектен за малките фенове на обичания извънземен герой.\n- Със свежи цветове и дизайн, вдъхновен от чаровния Стич, този часовник добавя весело настроение и характер към всяка детска визия.\n- Благодарение на LED дисплея с 12-часов формат, децата могат лесно да разпознават часа и датата, което го прави не само модерен, но и образователен.",
            ],
            [
                'name' => 'AUX кабел XO NB-R175A',
                'slug' => 'aux-kabel-xo-nb-r175a',
                'subtitle' => '3.5 мм · 1 м · черен',
                'brand' => 'xo',
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
                'image_path' => 'images/products/bluetooth-priemnik-hoco-e53.jpg',
                'price_in_stotinki' => 3895,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Безжичен Bluetooth аудио приемник, предназначен за използване в автомобил чрез AUX вход\nУстройството позволява да предавате музика безжично от вашия смартфон към аудиосистемата на колата и да провеждате разговори със свободни ръце (hands-free).\nКапацитет от 145 mAh, който осигурява до 10 часа време за музика или разговори.\nПълното зареждане отнема около 2 часа чрез type C -USB порт.",
            ],
        ];
    }

    /**
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
     * @return list<array<string, mixed>>
     */
    protected function toys(): array
    {
        return [
            [
                'name' => 'Метална кола BMW Z4 M40i',
                'slug' => 'metalna-kola-bmw-z4-m40i',
                'subtitle' => 'Мащаб 1:24 · светлини и звуци',
                'brand' => null,
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
                'image_path' => 'images/products/metalna-kola-mitsubishi-lancer-evo.jpg',
                'price_in_stotinki' => 8995,
                'compare_at_price_in_stotinki' => null,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Метална количка Mitsubishi Lancer Evolution - Легендата на пътя\nДобавете мощ и стил към своята колекция с този изключително детайлен метален модел на Mitsubishi Lancer Evolution.\nИзработена в мащаб 1:24, тази количка улавя агресивния дух и емблематичния дизайн на японската рали легенда.\nПерфектен избор както за запалени колекционери, така и за подарък на малки и големи фенове на високите скорости!",
            ],
            [
                'name' => 'Фигура Son Goku',
                'slug' => 'figura-son-goku-32cm',
                'subtitle' => 'Dragon Ball · 32 см',
                'brand' => null,
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
                'image_path' => 'images/products/figura-yoriichi-tsugikuni-30cm.png',
                'price_in_stotinki' => 8995,
                'compare_at_price_in_stotinki' => 17990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Пренесете легендарната сила от вселената на анимето Demon Slayer право във вашия дом с тази премиум, високо детайлна фигура на Йоричи Цугикуни.\nКато създател на първия дихателен стил (Слънчево дишане) и най-могъщия истребител на демони в историята, Йоричи е задължително попълнение за всеки истински фен и колекционер.\nСтатуетката се отличава с изключително ниво на прецизност при изработката.",
            ],
            [
                'name' => 'Конструктор Lamborghini Racing',
                'slug' => 'konstruktor-lamborghini-racing-rc',
                'subtitle' => '366 части · с дистанционно',
                'brand' => null,
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
                'image_path' => 'images/products/kamion-za-bokluk.jpg',
                'price_in_stotinki' => 3995,
                'compare_at_price_in_stotinki' => 7990,
                'is_new' => false,
                'is_featured' => false,
                'description' => "Комплектът съдържа:\n- камион,\n- 2 контейнера за боклук.\nФункции на камиона:\n- инерционен,\n- специализирани сигнални светлини,\n- специализирани реалистични звуци и сирени,\n- можете да сортирате боклука разделно - в отделни контейнери,\n- бутони за пневматично повдигане и сваляне на контейнера - посредством въздушно налягане,\n- мащаб - 1:16,\n- размери - 27.5 * 10.5 * 12.5 см.\n- цвят - оранжев, зелен и син\nКамионът е зареден с необходимите батерии.",
            ],
        ];
    }
}
