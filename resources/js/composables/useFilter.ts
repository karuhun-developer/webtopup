import { router } from '@inertiajs/vue3';

export function useFilter() {
    const updateParams = (newParams: Record<string, any>) => {
        const currentParams = Object.fromEntries(
            new URLSearchParams(window.location.search).entries(),
        );

        router.get(
            window.location.pathname,
            {
                ...currentParams, // Keep existing params
                ...newParams,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    return {
        updateParams,
    };
}
