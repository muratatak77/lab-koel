import { describe, expect, it } from 'vite-plus/test'
import { createHarness } from '@/__tests__/TestHarness'
import { http } from '@/services/http'
import { songNoteService } from './songNoteService'

describe('songNoteService', () => {
  const h = createHarness()

  it('fetches the note of a song', async () => {
    const song = h.factory('song').make()
    const note = h.factory('song-note').make({ song_id: song.id })
    const getMock = h.mock(http, 'get').mockResolvedValue(note)

    expect(await songNoteService.fetchNote(song)).toEqual(note)
    expect(getMock).toHaveBeenCalledWith(`songs/${song.id}/note`)
  })

  it('resolves to null when the song has no note', async () => {
    h.mock(http, 'get').mockResolvedValue('')

    expect(await songNoteService.fetchNote(h.factory('song').make())).toBeNull()
  })

  it('saves the note of a song', async () => {
    const song = h.factory('song').make()
    const note = h.factory('song-note').make({ song_id: song.id, content: 'Lovely bridge' })
    const putMock = h.mock(http, 'put').mockResolvedValue(note)

    expect(await songNoteService.saveNote(song, 'Lovely bridge')).toEqual(note)
    expect(putMock).toHaveBeenCalledWith(`songs/${song.id}/note`, { content: 'Lovely bridge' })
  })

  it('deletes the note of a song', async () => {
    const song = h.factory('song').make()
    const deleteMock = h.mock(http, 'delete').mockResolvedValue('')

    await songNoteService.deleteNote(song)

    expect(deleteMock).toHaveBeenCalledWith(`songs/${song.id}/note`)
  })
})
