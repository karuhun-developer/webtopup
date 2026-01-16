import { AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';

export function usePermission() {
    const hasPermission = (name: string) => {
        const page = usePage<AppPageProps>();
        const permissions = page.props.auth?.user?.permissions || [];

        return permissions.includes(name);
    };

    return { hasPermission };
}
