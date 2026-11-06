<template>
  <form class="md:w-[480px] min-w-full" @submit.prevent="handleSubmit" @keydown.esc="maybeClose">
    <header>
      <h1>Note</h1>
    </header>

    <main class="space-y-3">
      <p class="text-k-fg-70">Only you can see your note on “{{ song.title }}”.</p>
      <FormRow>
        <TextArea
          v-model="data.content"
          v-koel-focus
          :maxlength="MAX_CONTENT_LENGTH"
          name="content"
          placeholder="What do you want to remember about this song?"
          required
        />
      </FormRow>
    </main>

    <footer>
      <Btn :disabled="loading" type="submit">Save</Btn>
      <Btn variant="neutral" @click.prevent="maybeClose">Cancel</Btn>
      <Btn v-if="note" :disabled="loading" class="ml-auto" variant="destructive" @click.prevent="deleteNote">
        Delete Note
      </Btn>
    </footer>
  </form>
</template>

<script lang="ts" setup>
import { songNoteService } from '@/services/songNoteService'
import { useDialogBox } from '@/composables/useDialogBox'
import { useErrorHandler } from '@/composables/useErrorHandler'
import { useForm } from '@/composables/useForm'
import { useMessageToaster } from '@/composables/useMessageToaster'

import Btn from '@/components/ui/form/Btn.vue'
import FormRow from '@/components/ui/form/FormRow.vue'
import TextArea from '@/components/ui/form/TextArea.vue'

const props = defineProps<{ song: Song; note: SongNote | null }>()
const emit = defineEmits<{ (e: 'close'): void }>()

const MAX_CONTENT_LENGTH = 10_000

const { song, note } = props

const { showConfirmDialog } = useDialogBox()
const { toastSuccess } = useMessageToaster()

const close = () => emit('close')

const { data, loading, handleSubmit, maybeClose } = useForm<Pick<SongNote, 'content'>>({
  initialValues: {
    content: note?.content ?? '',
  },
  validator: ({ content }) => content.trim().length > 0,
  toast: {
    loading: 'Saving note…',
    success: 'Note saved.',
  },
  onSubmit: async ({ content }) => await songNoteService.saveNote(song, content.trim()),
  onSuccess: close,
  onClose: close,
})

const deleteNote = async () => {
  if (!(await showConfirmDialog('Delete this note?'))) {
    return
  }

  try {
    await songNoteService.deleteNote(song)
    toastSuccess('Note deleted.')
    close()
  } catch (error: unknown) {
    useErrorHandler().handleHttpError(error)
  }
}
</script>
