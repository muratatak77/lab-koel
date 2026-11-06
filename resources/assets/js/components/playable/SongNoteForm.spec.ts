import { screen, waitFor } from '@testing-library/vue'
import { describe, expect, it } from 'vite-plus/test'
import { createHarness } from '@/__tests__/TestHarness'
import { DialogBoxStub } from '@/__tests__/stubs'
import { songNoteService } from '@/services/songNoteService'
import Component from './SongNoteForm.vue'

describe('songNoteForm.vue', () => {
  const h = createHarness()

  const renderComponent = (note: SongNote | null = null) => {
    const song = h.factory('song').make({ title: 'Hey Jude' })

    const rendered = h.render(Component, {
      props: {
        song,
        note,
      },
    })

    return {
      ...rendered,
      song,
    }
  }

  it('creates a note', async () => {
    const saveMock = h.mock(songNoteService, 'saveNote')
    const { song, emitted } = renderComponent()

    screen.getByText('Only you can see your note on “Hey Jude”.')

    await h.type(screen.getByRole('textbox', { name: 'content' }), '  Na na na  ')
    await h.user.click(screen.getByRole('button', { name: 'Save' }))

    await waitFor(() => expect(saveMock).toHaveBeenCalledWith(song, 'Na na na'))
    expect(emitted().close).toBeTruthy()
  })

  it('edits an existing note', async () => {
    const note = h.factory('song-note').make({ content: 'First draft' })
    const saveMock = h.mock(songNoteService, 'saveNote')
    const { song } = renderComponent(note)

    await h.type(screen.getByDisplayValue('First draft'), 'Second draft')
    await h.user.click(screen.getByRole('button', { name: 'Save' }))

    await waitFor(() => expect(saveMock).toHaveBeenCalledWith(song, 'Second draft'))
  })

  it('does not save a blank note', async () => {
    const saveMock = h.mock(songNoteService, 'saveNote')
    renderComponent()

    await h.type(screen.getByRole('textbox', { name: 'content' }), '   ')
    await h.user.click(screen.getByRole('button', { name: 'Save' }))

    expect(saveMock).not.toHaveBeenCalled()
  })

  it('deletes the note after confirmation', async () => {
    h.mock(DialogBoxStub.value, 'confirm', true)
    const deleteMock = h.mock(songNoteService, 'deleteNote')
    const { song, emitted } = renderComponent(h.factory('song-note').make())

    await h.user.click(screen.getByRole('button', { name: 'Delete Note' }))

    await waitFor(() => expect(deleteMock).toHaveBeenCalledWith(song))
    expect(emitted().close).toBeTruthy()
  })

  it('keeps the note if deletion is not confirmed', async () => {
    h.mock(DialogBoxStub.value, 'confirm', false)
    const deleteMock = h.mock(songNoteService, 'deleteNote')
    renderComponent(h.factory('song-note').make())

    await h.user.click(screen.getByRole('button', { name: 'Delete Note' }))

    expect(deleteMock).not.toHaveBeenCalled()
  })

  it('does not offer deletion for a song without a note', () => {
    renderComponent()

    expect(screen.queryByRole('button', { name: 'Delete Note' })).toBeNull()
  })
})
