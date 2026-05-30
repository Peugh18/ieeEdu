import type { LucideIcon } from 'lucide-vue-next';

export interface SubscriptionUser {
    id: number;
    name: string;
    email: string;
}

export interface Subscription {
    id: number;
    user: SubscriptionUser;
    type: 'trimestral' | 'semestral' | 'anual';
    start_date: string;
    end_date: string;
    status: 'activa' | 'expirada' | 'cancelada';
    payment_amount?: number;
    payment_capture?: string;
    payment_status?: string;
}

export interface SubscriptionStatusConfig {
    label: string;
    cls: string;
    icon: LucideIcon;
}

export interface SubscriptionFilters {
    search?: string;
}
