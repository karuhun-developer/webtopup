export interface PPOBCategoryDataItem {
    id: number;
    name: string;
    slug: string;
    description?: string;
    image?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
    brands_count?: number;
}

export interface PPOBBrandDataItem {
    id: number;
    p_p_o_b_category_id: number;
    name: string;
    provider: string;
    slug: string;
    description?: string;
    featured: boolean;
    order: number;
    settings?: {
        type?: 'id' | 'id+server';
        label_id?: string;
        label_server?: string;
        servers?: string[];
    };
    image?: string;
    banner?: string;
    default_product_image?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
    category?: PPOBCategoryDataItem;
    products?: PPOBProductDataItem[];
}

export interface PPOBProductDataItem {
    id: number;
    p_p_o_b_brand_id: number;
    name: string;
    slug: string;
    sku: string;
    description?: string;
    buy_price: number;
    sell_price: number;
    image?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
    brand?: PPOBBrandDataItem;
}

export interface PPOBDepositDataItem {
    id: number;
    bank: string;
    payment_method: string;
    owner_name: string;
    account_number: string;
    amount: number;
    notes?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
}

export interface DigiflazzProductDataItem {
    product_name: string;
    category: string;
    brand: string;
    type: string;
    seller_name: string;
    price: number;
    buyer_sku_code: string;
    buyer_product_status: boolean;
    seller_product_status: boolean;
    unlimited_stock: boolean;
    stock: number;
    multi: boolean;
    start_cut_off: string;
    end_cut_off: string;
    desc: string;
}

export interface DigiflazzBrandGroup {
    name: string;
    products: DigiflazzProductDataItem[];
}

export interface DigiflazzCategoryGroup {
    name: string;
    brands: DigiflazzBrandGroup[];
}
