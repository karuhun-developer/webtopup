import { PPOBBrandDataItem } from '@/types/cms/ppob';
import type { UseQueryReturnType } from '@tanstack/vue-query';
import { useQuery } from '@tanstack/vue-query';
import axios from 'axios';
import { MaybeRef, unref } from 'vue';

export default function usePPOBQuery() {
    const fetchBrands = (
        categoryId: MaybeRef<number | null>,
        staleTime: number = 0,
    ): UseQueryReturnType<PPOBBrandDataItem[], Error> => {
        return useQuery({
            queryKey: ['fetchPPOBBrands', categoryId],
            queryFn: async () => {
                const id = unref(categoryId);

                if (!id) return [];

                const res = await axios.post('/cms/ppob/brands/json-all', {
                    category_id: id,
                });

                if (res.status !== 200) {
                    throw new Error('Failed to fetch PPOB brands');
                }

                return res.data.data;
            },
            staleTime,
        });
    };

    return {
        fetchBrands,
    };
}
