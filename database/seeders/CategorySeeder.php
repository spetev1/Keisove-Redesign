<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The storefront's taxonomy, taken from the approved design document.
     *
     * The design shows it twice, at two levels. Its nav row names the
     * departments - Калъфи, Протектори, Зарядни, Слушалки, Power Bank, Арабски
     * парфюми - and its category grid pictures what those divide into:
     * силиконови гърбове, кожени калъфи, твърди гърбове, USB кабели, Bluetooth
     * слушалки and the rest. So the row is the parents and the grid is mostly
     * the children, and both are seeded here as one tree.
     *
     * The first six are the nav row, in the design's order. The five after them
     * are departments the row does not name but the catalogue needs.
     *
     * One split the design does *not* make is by handset. The live store carries
     * a category per phone - "Силиконов гръб за Samsung" and twenty-two more -
     * and those stay the `device_family` filter here: a row per handset would
     * mean two URLs for one set of products and facet counts split between them.
     *
     * "Промоции" is absent on purpose: it is a filtered view over all of this,
     * not a department of its own.
     */
    public function run(): void
    {
        $seeded = [];

        foreach ($this->departments() as $sortOrder => $department) {
            $children = $department['children'] ?? [];
            unset($department['children']);

            $parent = $this->upsert($department, $sortOrder + 1);
            $seeded[] = $parent->slug;

            foreach ($children as $childOrder => $child) {
                $seeded[] = $this->upsert(
                    $child,
                    $childOrder + 1,
                    $parent,
                )->slug;
            }
        }

        $this->prune($seeded);
    }

    /**
     * Drops categories this taxonomy no longer has.
     *
     * Only empty ones go. A category still holding products is left alone
     * rather than dragging them down with it, so re-seeding an older database
     * cannot lose rows; `migrate:fresh --seed` is what moves it over cleanly.
     *
     * @param  list<string>  $seeded
     */
    protected function prune(array $seeded): void
    {
        Category::query()
            ->whereNotIn('slug', $seeded)
            ->whereDoesntHave('products')
            ->whereDoesntHave('children')
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $category
     */
    protected function upsert(
        array $category,
        int $sortOrder,
        ?Category $parent = null,
    ): Category {
        return Category::updateOrCreate(
            ['slug' => $category['slug']],
            [
                ...$category,
                'sort_order' => $sortOrder,
                'parent_id' => $parent?->id,
            ],
        );
    }

    /**
     * Artwork is only named here for the three departments that have
     * photography of their own. Everything else borrows a product shot from its
     * own shelf once the products are seeded - see CategoryArtworkSeeder.
     *
     * @return list<array<string, mixed>>
     */
    protected function departments(): array
    {
        return [
            /* ---------- the design's nav row, in the design's order ---------- */
            [
                'name' => 'Калъфи',
                'slug' => 'kalafi',
                'tagline' => 'За всеки телефон. Всеки стил.',
                'image_path' => 'images/categories/keisove.jpg',
                'children' => [
                    [
                        'name' => 'Силиконови гърбове',
                        'slug' => 'silikonovi-garbove',
                        'tagline' => 'TPU и хибридни, за всеки телефон.',
                    ],
                    [
                        'name' => 'Кожени калъфи',
                        'slug' => 'kozheni-kalafi',
                        'tagline' => 'Тефтери, кобури и калъфи със стойка.',
                    ],
                    [
                        'name' => 'Твърди гърбове',
                        'slug' => 'tvardi-garbove',
                        'tagline' => 'Поликарбонат, метал и защита за камерата.',
                    ],
                    [
                        'name' => 'Калъфи за таблети',
                        'slug' => 'kalafi-za-tableti',
                        'tagline' => 'По размер, от 7 до 11 инча.',
                    ],
                    [
                        'name' => 'Универсални калъфи',
                        'slug' => 'universalni-kalafi',
                        'tagline' => 'Чанти, джобове и водоустойчиви калъфи.',
                    ],
                ],
            ],
            [
                'name' => 'Протектори',
                'slug' => 'protektori',
                'tagline' => 'Защита за екрана и камерата.',
                'image_path' => 'images/categories/protektori.jpg',
            ],
            [
                'name' => 'Зарядни',
                'slug' => 'zaryadni',
                'tagline' => 'Зарядни, кабели и адаптери.',
                'children' => [
                    [
                        'name' => 'Зарядни 220V',
                        'slug' => 'zaryadni-220v',
                        'tagline' => 'За контакт, от 20 до 100 вата.',
                    ],
                    [
                        'name' => 'Зарядни за кола 12V',
                        'slug' => 'zaryadni-12v',
                        'tagline' => 'Адаптери и FM трансмитери.',
                    ],
                    [
                        'name' => 'USB кабели',
                        'slug' => 'usb-kabeli',
                        'tagline' => 'Type-C, Lightning и Micro USB.',
                    ],
                    [
                        'name' => 'Преходници и адаптери',
                        'slug' => 'prehodnitsi-i-adapteri',
                        'tagline' => 'AUX, Bluetooth приемници и преходници.',
                    ],
                ],
            ],
            [
                'name' => 'Слушалки',
                'slug' => 'slushalki',
                'tagline' => 'Безжични, с кабел и колонки.',
                'children' => [
                    [
                        'name' => 'Bluetooth слушалки',
                        'slug' => 'bluetooth-slushalki',
                        'tagline' => 'Безжични, TWS и over-ear.',
                    ],
                    [
                        'name' => 'Handsfree',
                        'slug' => 'handsfree',
                        'tagline' => 'С кабел, Type-C и 3.5 мм.',
                    ],
                    [
                        'name' => 'Тонколони',
                        'slug' => 'tonkoloni',
                        'tagline' => 'Преносими Bluetooth колонки.',
                    ],
                ],
            ],
            [
                'name' => 'Power Bank',
                'slug' => 'power-bank',
                'tagline' => 'Външни батерии, с MagSafe и без.',
            ],
            [
                'name' => 'Арабски парфюми',
                'slug' => 'parfyumi',
                // The design's own subtitle for this tile.
                'tagline' => 'Дамски · Мъжки · Унисекс',
                'image_path' => 'images/categories/parfyumi.png',
                'children' => [
                    [
                        'name' => 'Дамски',
                        'slug' => 'damski-parfyumi',
                        'tagline' => 'Цветни, ориенталски и гурме.',
                    ],
                    [
                        'name' => 'Мъжки',
                        'slug' => 'mazhki-parfyumi',
                        'tagline' => 'Дървесни, подправени и уд.',
                    ],
                    [
                        'name' => 'Унисекс',
                        'slug' => 'uniseks-parfyumi',
                        'tagline' => 'Аромати без предназначение.',
                    ],
                ],
            ],

            /* ---------- departments the nav row does not name ---------- */
            [
                'name' => 'Смарт часовници',
                'slug' => 'smart-chasovnitsi',
                'tagline' => 'За възрастни и за деца.',
                'children' => [
                    [
                        'name' => 'Аксесоари за часовници',
                        'slug' => 'aksesoari-za-chasovnitsi',
                        'tagline' => 'Каишки и безжични зарядни.',
                    ],
                ],
            ],
            [
                'name' => 'Стойки за кола',
                'slug' => 'stoyki-za-kola',
                'tagline' => 'По модел телефон, за таблото или парбриза.',
            ],
            [
                'name' => 'Оригинални батерии',
                'slug' => 'originalni-baterii',
                'tagline' => 'За смяна, по модел телефон.',
            ],
            [
                'name' => 'Карти памет',
                'slug' => 'karti-pamet',
                'tagline' => 'Micro SD и SD, с адаптер.',
            ],
            [
                'name' => 'Детски играчки',
                'slug' => 'detski-igrachki',
                'tagline' => 'Играчки, които заслужават разопаковане.',
                'children' => [
                    [
                        'name' => 'Коли',
                        'slug' => 'koli',
                        'tagline' => 'Метални модели и с дистанционно.',
                    ],
                    [
                        'name' => 'Занимателни',
                        'slug' => 'zanimatelni',
                        'tagline' => 'Конструктори, плюшени и хеликоптери.',
                    ],
                    [
                        'name' => 'Колекционерски фигурки',
                        'slug' => 'kolektsionerski-figurki',
                        'tagline' => 'Аниме и филмови герои.',
                    ],
                ],
            ],
        ];
    }
}
