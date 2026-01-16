<?php

namespace App\Services;

use App\Models\CropsName;
use App\Models\User;

class MenuService
{
    protected $user;
    protected $appType;
    
    // Define crop type groups for easier management
    const CROP_GROUPS = [
        'certification' => [CropsName::CROP_TYPE_1, CropsName::CROP_TYPE_3, CropsName::CROP_TYPE_4, CropsName::CROP_TYPE_5],
        'sertificate_protocol' => [CropsName::CROP_TYPE_3, CropsName::CROP_TYPE_4],
        'quality_certificates_only' => [CropsName::CROP_TYPE_2],
        'laboratory' => [CropsName::CROP_TYPE_1],
        'hvi_enabled' => [CropsName::CROP_TYPE_1, CropsName::CROP_TYPE_3, CropsName::CROP_TYPE_4],
        'lclass_enabled' => [CropsName::CROP_TYPE_4],
        'product_conclusion' => [CropsName::CROP_TYPE_5],
    ];

    public function __construct()
    {
        $this->user = auth()->user();
        $this->appType = getApplicationType();
    }

    /**
     * Check if current app type is in a group
     */
    protected function inGroup(string $group): bool
    {
        return in_array($this->appType, self::CROP_GROUPS[$group] ?? []);
    }

    /**
     * Check if current app type is NOT in a group
     */
    protected function notInGroup(string $group): bool
    {
        return !$this->inGroup($group);
    }

    /**
     * Check if user has specific role
     */
    protected function hasRole(string $role): bool
    {
        return $this->user->role === $role;
    }

    /**
     * Check if user does NOT have specific role
     */
    protected function notRole(string $role): bool
    {
        return $this->user->role !== $role;
    }

    /**
     * Get menu configuration based on user role and application type
     */
    public function getMenuConfig()
    {
        return [
            [
                'type' => 'item',
                'label' => 'message.Bosh sahifa',
                'icon' => 'cil-home',
                'route' => 'home',
                'visible' => true,
            ],
            [
                'type' => 'group',
                'label' => 'message.Hisobotlar',
                'icon' => 'cil-folder',
                'visible' => true,
                'children' => [
                    [
                        'type' => 'item',
                        'label' => "message.Umumiy ro'yxat",
                        'icon' => 'cil-list',
                        'route' => 'report.full_report',
                    ],
                    [
                        'type' => 'item',
                        'label' => "message.Hududlar kesimda ma'lumot",
                        'icon' => 'cil-map',
                        'route' => '/vue/state-report',
                    ],
                    [
                        'type' => 'item',
                        'label' => "message.Korxonalar kesimda ma'lumot",
                        'icon' => 'cil-building',
                        'route' => 'report.company_report',
                    ],
                ],
            ],
            
            // Quality Certificates Section (only for CROP_TYPE_2)
            [
                'type' => 'title',
                'label' => 'message.Sifat Sertifikatlari',
                'visible' => $this->inGroup('quality_certificates_only'),
            ],
            [
                'type' => 'item',
                'label' => 'Sifat sertifikatlari',
                'icon' => 'cil-book',
                'route' => '/sifat-sertificates2/list',
                'activePattern' => 'sifat-sertificates2/*',
                'visible' => $this->inGroup('quality_certificates_only'),
            ],
            
            // Certification Section (all except CROP_TYPE_2)
            [
                'type' => 'title',
                'label' => 'message.Sertifikatsiya',
                'visible' => $this->inGroup('certification'),
            ],
            [
                'type' => 'item',
                'label' => 'message.Arizalar',
                'icon' => 'cil-cursor',
                'route' => '/application/list',
                'activePattern' => 'application/*',
                'visible' => $this->inGroup('certification'),
            ],
            [
                'type' => 'item',
                'label' => 'message.Qaror va Sinov dasturlari',
                'icon' => 'cil-folder-open',
                'route' => '/decision/search',
                'activePattern' => 'decision/*',
                'visible' => $this->inGroup('laboratory'),
            ],
            [
                'type' => 'item',
                'label' => "message.Na'muna olish dalolatnomalari",
                'icon' => 'cil-inbox',
                'route' => '/dalolatnoma/search',
                'activePattern' => 'dalolatnoma/*',
                'visible' => $this->inGroup('certification'),
            ],
            [
                'type' => 'item',
                'label' => "message.Og'irlik bo'yicha dalolatnomalar",
                'icon' => 'cil-balance-scale',
                'route' => '/akt_amount/search',
                'activePattern' => 'akt_amount/*',
                'visible' => $this->inGroup('certification'),
            ],
            [
                'type' => 'item',
                'label' => 'message.Namlik dalolatnomasi',
                'icon' => 'cil-eyedropper',
                'route' => '/humidity/search',
                'activePattern' => 'humidity/*',
                'visible' => $this->inGroup('laboratory'),
            ],
            
            // Laboratory Section
            [
                'type' => 'title',
                'label' => 'message.Laboratoriya',
                'visible' => $this->inGroup('laboratory'),
            ],
            [
                'type' => 'item',
                'label' => $this->inGroup('lclass_enabled') 
                    ? "Lclass ma'lumotlari" 
                    : "message.HVI ma'lumotlari",
                'icon' => 'cil-devices',
                'route' => '/hvi/list',
                'activePattern' => 'hvi/*',
                'visible' => $this->inGroup('hvi_enabled'),
            ],
            [
                'type' => 'item',
                'label' => 'message.Namlik natijalari',
                'icon' => 'cil-chart',
                'route' => '/humidity_result/search',
                'activePattern' => 'humidity_result/*',
                'visible' => $this->inGroup('laboratory'),
            ],
            [
                'type' => 'item',
                'label' => "message.O'lchash xatoligi",
                'icon' => 'cil-clear-all',
                'route' => '/measurement_mistake/search',
                'activePattern' => 'measurement_mistake/*',
                'visible' => $this->inGroup('laboratory'),
            ],
            
            // Test Protocols - Different routes for different crop types
            [
                'type' => 'item',
                'label' => 'message.Sinov bayonnomalari',
                'icon' => 'cil-list',
                'route' => '/laboratory-protocol/list',
                'activePattern' => 'laboratory-protocol/*',
                'visible' => $this->inGroup('laboratory'),
            ],
            [
                'type' => 'item',
                'label' => 'message.Sinov bayonnomalari',
                'icon' => 'cil-list',
                'route' => '/sertificate-protocol/list',
                'activePattern' => 'sertificate-protocol/*',
                'visible' => $this->inGroup('sertificate_protocol'),
            ],
             [
                'type' => 'item',
                'label' => 'Maxsus Xulosalar',
                'icon' => 'cil-list',
                'route' => '/product-conclusion/list',
                'activePattern' => 'product-conclusion/*',
                'visible' => $this->inGroup('product_conclusion'),
            ],
            [
                'type' => 'item',
                'label' => 'message.Yakuniy natijalar',
                'icon' => 'cil-bar-chart',
                'route' => '/final_results/search',
                'activePattern' => 'final_results/*',
                'visible' => $this->inGroup('certification'),
            ],
            
            // System Settings
            [
                'type' => 'title',
                'label' => 'message.Tizim sozlamalari',
                'visible' => true,
            ],
            [
                'type' => 'group',
                'label' => 'message.Mahsulotlar',
                'icon' => 'cil-plant',
                'visible' => $this->notRole(User::ROLE_DIROCTOR),
                'children' => [
                    [
                        'type' => 'item',
                        'label' => "message.Nomlar ro'yxati",
                        'icon' => 'cil-list',
                        'route' => '/crops_name/list',
                        'activePattern' => 'crops_name/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => "message.Navlar ro'yxati",
                        'icon' => 'cil-list',
                        'route' => '/crops_type/list',
                        'activePattern' => 'crops_type/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => "message.Sinflar ro'yxatii",
                        'icon' => 'cil-list',
                        'route' => '/crops_generation/list',
                        'activePattern' => 'crops_generation/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => 'message.Seleksiya turlari',
                        'icon' => 'cil-list',
                        'route' => '/crops_selection/list',
                        'activePattern' => 'crops_selection/*',
                    ],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'message.Korxona va tashkilotlar',
                'icon' => 'cil-factory',
                'visible' => true,
                'children' => [
                    [
                        'type' => 'item',
                        'label' => 'message.Buyurtmachilar korxonalar',
                        'icon' => 'cil-building',
                        'route' => '/organization/list',
                        'activePattern' => 'organization/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => 'message.Ishlab chiqaruvchi zavodlar',
                        'icon' => 'cil-institution',
                        'route' => '/prepared/list',
                        'activePattern' => 'prepared/*',
                    ],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'message.Laboratoriya sozlamalari',
                'icon' => 'cil-settings',
                'visible' => $this->notInGroup('quality_certificates_only'),
                'children' => [
                    [
                        'type' => 'item',
                        'label' => 'message.Laboratoriyalar',
                        'icon' => 'cil-beaker',
                        'route' => '/laboratories/list',
                        'activePattern' => 'laboratories/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => "message.In Xaus ma'lumotlari",
                        'icon' => 'cil-filter-x',
                        'route' => '/in_xaus/list',
                        'activePattern' => 'in_xaus/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => 'message.Klassiyorlar',
                        'icon' => 'cil-group',
                        'route' => '/klassiyor/list',
                        'activePattern' => 'klassiyor/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => 'message.Operatorlar',
                        'icon' => 'cil-list',
                        'route' => 'laboratory_operators.index',
                        'activePattern' => 'laboratory_operators/*',
                    ],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'message.Normativ hujjatlar',
                'icon' => 'cil-command',
                'visible' => $this->notInGroup('quality_certificates_only') 
                    && $this->notRole(User::STATE_EMPLOYEE),
                'children' => [
                    [
                        'type' => 'item',
                        'label' => 'message.Normativ hujjatlar',
                        'icon' => 'cil-file',
                        'route' => '/nds/list',
                        'activePattern' => 'nds/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => "message.Sifat ko'rsatkichlari",
                        'icon' => 'cil-paperclip',
                        'route' => '/indicator/list',
                        'activePattern' => 'indicator/*',
                    ],
                ],
            ],
            
            // Admin Settings
            [
                'type' => 'title',
                'label' => 'message.Sozlamalar',
                'visible' => $this->hasRole('admin'),
            ],
            [
                'type' => 'group',
                'label' => 'message.Foydalanuvchilar',
                'icon' => 'cil-user',
                'visible' => $this->hasRole('admin'),
                'children' => [
                    [
                        'type' => 'item',
                        'label' => "app.Ro'yxat",
                        'icon' => 'cil-group',
                        'route' => '/employee/list',
                        'activePattern' => 'employee/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => "app.Qo'shish",
                        'icon' => 'cil-user-plus',
                        'route' => '/employee/add',
                    ],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'message.Hududlar',
                'icon' => 'cil-map',
                'visible' => $this->hasRole('admin'),
                'children' => [
                    [
                        'type' => 'item',
                        'label' => 'message.Viloyatlar',
                        'icon' => 'cis-map',
                        'route' => '/states/list',
                        'activePattern' => 'states/*',
                    ],
                    [
                        'type' => 'item',
                        'label' => 'message.Shaxar va tumanlar',
                        'icon' => 'cil-city',
                        'route' => '/cities/list',
                        'activePattern' => 'cities/*',
                    ],
                ],
            ],
        ];
    }

    /**
     * Filter menu items based on visibility
     */
    public function getVisibleMenuItems()
    {
        return collect($this->getMenuConfig())
            ->filter(function ($item) {
                return !isset($item['visible']) || $item['visible'] === true;
            })
            ->map(function ($item) {
                if (isset($item['children'])) {
                    $item['children'] = collect($item['children'])
                        ->filter(function ($child) {
                            return !isset($child['visible']) || $child['visible'] === true;
                        })
                        ->toArray();
                }
                return $item;
            })
            ->toArray();
    }
}