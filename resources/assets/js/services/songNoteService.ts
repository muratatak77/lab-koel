import { http } from '@/services/http'

export const songNoteService = {
  async fetchNote(song: Song) {
    const note = await http.get<SongNote | ''>(`songs/${song.id}/note`)

    return note || null
  },

  async saveNote(song: Song, content: string) {
    return await http.put<SongNote>(`songs/${song.id}/note`, { content })
  },

  async deleteNote(song: Song) {
    await http.delete(`songs/${song.id}/note`)
  },
}
