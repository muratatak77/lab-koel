import { faker } from '@faker-js/faker'

export default (): SongNote => ({
  type: 'song-notes',
  id: faker.number.int({ min: 1 }),
  song_id: faker.string.uuid(),
  content: faker.lorem.paragraph(),
  created_at: faker.date.past().toISOString(),
  updated_at: faker.date.recent().toISOString(),
})
