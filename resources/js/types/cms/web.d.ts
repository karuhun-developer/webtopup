export interface SliderDataItem {
    id: number;
    title: string;
    order: number;
    image?: string;
    link?: string;
    status: boolean;
    created_at: string;
    updated_at: string;
}

export interface FaqDataItem {
    id: number;
    question: string;
    answer: string;
    order: number;
    status: boolean;
    created_at: string;
    updated_at: string;
}
