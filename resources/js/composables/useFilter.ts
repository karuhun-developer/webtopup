import { router } from '@inertiajs/vue3';

export function useFilter() {
    const updateParams = (newParams: Record<string, any>) => {
        const currentParams = Object.fromEntries(
            new URLSearchParams(window.location.search).entries(),
        );

        // Remove array parameters from current params that are being updated
        // This prevents accumulation of array values
        Object.keys(newParams).forEach((key) => {
            // Remove any existing array-style parameters (e.g., key[0], key[1], etc.)
            Object.keys(currentParams).forEach((currentKey) => {
                if (currentKey === key || currentKey.startsWith(`${key}[`)) {
                    delete currentParams[currentKey];
                }
            });
        });

        router.get(
            window.location.pathname,
            {
                ...currentParams, // Keep existing params (with arrays removed)
                ...newParams, // Add new params
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
