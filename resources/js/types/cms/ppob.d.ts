export interface PPOBCategoryDataItem {
    id: number;
    name: string;
    slug: string;
    description?: string;
    image?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
}

export interface PPOBBrandDataItem {
    id: number;
    p_p_o_b_category_id: number;
    name: string;
    slug: string;
    description?: string;
    image?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
    category?: PPOBCategoryDataItem;
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
