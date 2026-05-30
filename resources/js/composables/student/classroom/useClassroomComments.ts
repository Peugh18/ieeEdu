import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useClassroomComments() {
    const newComment = ref('');
    const replyingTo = ref<{ id: number } | null>(null);
    const editingComment = ref<{ id: number; content: string } | null>(null);

    function postComment(lessonId: number) {
        if (!newComment.value.trim()) return;

        router.post(
            route('student.comments.store', { lesson: lessonId }),
            {
                content: newComment.value,
                parent_id: replyingTo.value?.id,
            },
            {
                onSuccess: () => {
                    newComment.value = '';
                    replyingTo.value = null;
                },
            }
        );
    }

    function toggleLike(commentId: number) {
        router.post(
            route('student.comments.like', { comment: commentId }),
            {},
            {
                preserveScroll: true,
            }
        );
    }

    function deleteComment(commentId: number) {
        if (confirm('¿Estás seguro de eliminar este comentario?')) {
            router.delete(route('student.comments.destroy', { comment: commentId }), {
                preserveScroll: true,
            });
        }
    }

    function updateComment() {
        if (!editingComment.value?.content.trim()) return;
        router.put(
            route('student.comments.update', { comment: editingComment.value.id }),
            {
                content: editingComment.value.content,
            },
            {
                onSuccess: () => {
                    editingComment.value = null;
                },
            }
        );
    }

    const timeSince = (dateString: string) => {
        const now = new Date();
        const date = new Date(dateString);
        const seconds = Math.floor((now.getTime() - date.getTime()) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) return 'hace ' + Math.floor(interval) + ' años';
        interval = seconds / 2592000;
        if (interval > 1) return 'hace ' + Math.floor(interval) + ' meses';
        interval = seconds / 86400;
        if (interval > 1) return 'hace ' + Math.floor(interval) + ' días';
        interval = seconds / 3600;
        if (interval > 1) return 'hace ' + Math.floor(interval) + ' horas';
        interval = seconds / 60;
        if (interval > 1) return 'hace ' + Math.floor(interval) + ' min';
        return 'hace un momento';
    };

    return {
        newComment,
        replyingTo,
        editingComment,
        postComment,
        toggleLike,
        deleteComment,
        updateComment,
        timeSince,
    };
}
