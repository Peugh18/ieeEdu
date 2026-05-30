export interface ConsultoriaBanner {
    heading: string | null;
    subheading: string | null;
    image_path: string | null;
    button_text: string | null;
    button_link: string | null;
    position: string | null;
    whatsapp_number: string | null;
    contact_email: string | null;
    contact_address: string | null;
}

export interface ConsultoriaProps {
    isDashboard?: boolean;
    banner?: ConsultoriaBanner | null;
}

export interface ConsultoriaPaso {
    n: string;
    titulo: string;
    desc: string;
    svg: string;
}
