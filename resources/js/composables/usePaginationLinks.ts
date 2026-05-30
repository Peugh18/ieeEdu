import { computed, ComputedRef } from 'vue';
import { PaginationLink } from '@/types/pagination';

export function usePaginationLinks(links: PaginationLink[] | undefined | null): ComputedRef<PaginationLink[]> {
    return computed(() => {
        if (!links) return [];
        return links.filter(link => link.url !== null);
    });
}
