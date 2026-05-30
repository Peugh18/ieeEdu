export function useClassroomPlayer(_completeLessonCallback: () => void) {
    const getYoutubeId = (url?: string) => {
        if (!url) return null;
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11 ? match[2] : null;
    };

    function initPlyr(onTimeUpdate: (currentTime: number, duration: number) => void) {
        // Load CSS
        if (!document.querySelector('link[href*="plyr.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.plyr.io/3.7.8/plyr.css';
            document.head.appendChild(link);
        }

        // Load JS
        const existingScript = document.querySelector('script[src*="plyr.js"]');
        const setupPlayer = () => {
            const players = document.querySelectorAll('.js-plyr');
            players.forEach((p) => {
                const PlyrCtor = (window as unknown as Record<string, unknown>).Plyr as new (element: Element, options: Record<string, unknown>) => { on: (event: string, cb: (e: { detail: { plyr: { currentTime: number; duration: number } } }) => void) => void };
                const player = new PlyrCtor(p, {
                    controls: [
                        'play-large',
                        'play',
                        'progress',
                        'current-time',
                        'mute',
                        'volume',
                        'fullscreen',
                    ],
                    tooltips: { controls: true, seek: true },
                    youtube: {
                        noCookie: false,
                        rel: 0,
                        showinfo: 0,
                        iv_load_policy: 3,
                        modestbranding: 1,
                        controls: 0,
                        fs: 1,
                        enablejsapi: 1,
                        playsinline: 1,
                        origin: window.location.origin,
                    },
                });

                player.on('timeupdate', (event: { detail: { plyr: { currentTime: number; duration: number } } }) => {
                    const instance = event.detail.plyr;
                    if (!instance.duration) return;
                    onTimeUpdate(instance.currentTime, instance.duration);
                });
            });
        };

        if (existingScript) {
            setupPlayer();
        } else {
            const script = document.createElement('script');
            script.src = 'https://cdn.plyr.io/3.7.8/plyr.js';
            script.onload = setupPlayer;
            document.head.appendChild(script);
        }
    }

    return {
        getYoutubeId,
        initPlyr,
    };
}
