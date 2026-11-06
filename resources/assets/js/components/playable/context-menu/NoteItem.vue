<template>
  <MenuItem v-if="song" @click="openNoteForm">Note…</MenuItem>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { defineAsyncComponent } from '@/utils/helpers'
import { isSong } from '@/utils/typeGuards'
import { songNoteService } from '@/services/songNoteService'
import { useContextMenu } from '@/composables/useContextMenu'
import { useErrorHandler } from '@/composables/useErrorHandler'
import { useModal } from '@/composables/useModal'

const props = defineProps<{ playables: Playable[] }>()

const SongNoteForm = defineAsyncComponent(() => import('@/components/playable/SongNoteForm.vue'))

const { MenuItem, trigger } = useContextMenu()
const { openModal } = useModal()

const song = computed(() => (props.playables.length === 1 && isSong(props.playables[0]) ? props.playables[0] : null))

const openNoteForm = () =>
  trigger(async () => {
    const targetSong = song.value!

    try {
      const note = await songNoteService.fetchNote(targetSong)
      openModal<'SONG_NOTE_FORM'>(SongNoteForm, { song: targetSong, note })
    } catch (error: unknown) {
      useErrorHandler().handleHttpError(error)
    }
  })
</script>
