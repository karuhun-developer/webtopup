import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck?: string | string[],
    currentUrl?: string,
) {
    // If it's an array, we treat them as route name patterns
    if (Array.isArray(urlToCheck)) {
        return urlToCheck.some((pattern) => {
            const url = toUrl(pattern);

            return url && currentUrl?.includes(url);
        });
    }

    // Fallback to string contains check
    const url = toUrl(urlToCheck);

    return url ? currentUrl?.includes(url) : false;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']> | undefined) {
    return typeof href === 'string' ? href : href?.url;
}

export function formatCurrency(amount: any) {
    return new Intl.NumberFormat('id-ID', {
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}
