<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CultureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cultures')->insert([
            [
                'id' => 1,
                'title' => 'Traditional Weaving of Sumba',
                'description_1' => 'Sumba traditional weaving, known as ikat, is a sacred and intricate art form that reflects the island’s deep cultural heritage. Each woven cloth is more than just fabric—it is a story, a symbol, and a spiritual connection passed down through generations. The motifs are not randomly chosen; they represent ancestral myths, social hierarchy, protective spirits, and even the weaver’s prayer for fertility or prosperity. For the Sumbanese, wearing or presenting a piece of ikat during ceremonies such as weddings, funerals, or harvest celebrations is a deeply symbolic act rooted in respect and tradition.',
                'description_2' => 'The process of creating a single ikat textile can take anywhere from several weeks to many months, depending on the complexity of the patterns. It begins with hand-spun cotton threads, which are tied and dyed repeatedly using natural ingredients from plants, roots, and minerals to achieve earthy tones of red, indigo, and brown. The dyed threads are then woven manually on traditional wooden looms, often by women who learned the skill from their mothers and grandmothers. Visiting a weaving village in Sumba is not just a visual experience—it is a cultural journey into the lives of the artisans who sustain this centuries-old tradition with reverence and precision.',
                'tags' => '["3-6 months per piece","Natural dyes","Centuries-old tradition"]',
                'created_at' => '2025-06-15 07:56:55',
                'updated_at' => '2025-06-15 07:58:37',
            ],
            [
                'id' => 2,
                'title' => 'Pasola (Sumba Spear-Throwing Festival)',
                'description_1' => 'Pasola is a traditional ritual of spear-throwing on horseback held in West Sumba, marking the beginning of the planting season. It serves as a sacred tradition to honor the ancestors and ask for a prosperous harvest.',
                'description_2' => 'The festival involves two groups of horsemen battling in a ceremonial war, symbolizing courage, sacrifice, and harmony with nature. It\'s held annually between February and March.',
                'tags' => '["Pasola","Sumba","Culture"]',
                'created_at' => '2025-06-15 10:12:34',
                'updated_at' => '2025-06-15 10:18:19',
            ],
            [
                'id' => 3,
                'title' => 'Caci Dance (Manggarai Whip & Shield Ritual)',
                'description_1' => 'Caci is a whip-and-shield dance from Manggarai (Flores), often performed during harvest festivals or thanksgiving rituals',
                'description_2' => 'Two men duel: one strikes with a whip, the other defends with a leather shield. The dance embodies courage, discipline, and community values. Elders encourage and accompany with cheers and music .',
                'tags' => '["Caci","Manggarai","Culture"]',
                'created_at' => '2025-06-15 10:19:51',
                'updated_at' => '2025-06-15 10:19:51',
            ],
            [
                'id' => 4,
                'title' => 'Tebe Dance (Timorese Solidarity Dance)',
                'description_1' => 'Tebe, a communal dance of Belu and Malaka, is performed in weddings, harvest celebrations, and as acts of gratitude, with participants singing and stomping in unison while holding hands ',
                'description_2' => 'The performance ends with dancers sitting together to share food, symbolizing unity. Historically, it commemorated returning warriors and fosters social solidarity',
                'tags' => '["Tebe","Timor","Culture"]',
                'created_at' => '2025-06-15 10:21:37',
                'updated_at' => '2025-06-15 10:21:37',
            ],
            [
                'id' => 5,
                'title' => 'Sasando (Rote String Instrument)',
                'description_1' => 'The sasando is a traditional tube-zither from Rote Island, crafted from bamboo with strings and a fan of palm leaves as resonance. Some models have up to 56 strings',
                'description_2' => 'Dating back to at least the 7th century, it’s used in ceremonies, rituals, and performances. Recently, electric versions have emerged, blending tradition with innovation .',
                'tags' => '["Sasando","Rote","Culture"]',
                'created_at' => '2025-06-15 10:23:03',
                'updated_at' => '2025-06-15 10:24:50',
            ],
            [
                'id' => 6,
                'title' => 'Reba Ceremony (Ngada New Year Ritual)',
                'description_1' => 'Reba is a traditional ceremony among the Ngada people of Flores celebrating the local New Year. It’s an annual thanksgiving event honoring ancestors and the earth ',
                'description_2' => 'Held in traditional communal houses, it fosters unity, solidarity, and communal well-being across social classes',
                'tags' => '["Reba","Ngada","Culture"]',
                'created_at' => '2025-06-15 10:25:30',
                'updated_at' => '2025-06-20 06:24:36',
            ],
            [
                'id' => 7,
                'title' => 'Elkoil Oot (Rain-Calling Gong Ceremony)',
                'description_1' => 'Elkoil Oot is a drought ritual in Flores invoking rain by sounding a sacred inherited gong',
                'description_2' => 'Led by tribal elders, it combines prayer and ritual strikes on the gong. The ceremony holds deep spiritual power; the gong is considered sacred and not to be used lightly .',
                'tags' => '["Elkoil Oot","Ritual","Culture"]',
                'created_at' => '2025-06-15 10:25:51',
                'updated_at' => '2025-06-20 06:22:50',
            ],
            [
                'id' => 8,
                'title' => 'Lota Script (Ende Traditional Writing System)',
                'description_1' => 'Lota script is an ancient writing tradition from Ende, Flores, derived from the Bugis script but with eight unique characters',
                'description_2' => 'Dating to the 16th century, it represents local identity and literacy during the reign of Sultan Alaudin; still valued as part of Flores heritage .',
                'tags' => '["Lota","Ende","Culture"]',
                'created_at' => '2025-06-15 10:26:08',
                'updated_at' => '2025-06-20 06:21:12',
            ],
            [
                'id' => 9,
                'title' => 'Golo Koe Festival (Labuan Bajo Religious-Cultural Carnival)',
                'description_1' => 'Held in early August in Labuan Bajo, Golo Koe is a Catholic festival celebrating the Assumption of Mary, with processions, cultural performances, and religious symbolism ',
                'description_2' => 'It features a cultural carnival, art exhibitions, and participation from multiple ethnic groups, promoting interfaith unity and regional pride',
                'tags' => '["Golo Koe","Labuan Bajo","Culture"]',
                'created_at' => '2025-06-15 10:26:28',
                'updated_at' => '2025-06-20 06:19:25',
            ],
            [
                'id' => 10,
                'title' => 'Traditional Boxing “Etu” (Nagekeo Combat Ritual)',
                'description_1' => 'Etu is an indigenous form of bare-knuckle boxing in Wulu Valley, Nagekeo. The event is held annually in February, linked to the traditional agricultural calendar ',
                'description_2' => 'This ritual combat for adult men reinforces masculinity, endurance, and community cohesion as part of a larger cultural tour ',
                'tags' => '["Etu","Nagekeo","Culture"]',
                'created_at' => '2025-06-15 10:26:47',
                'updated_at' => '2025-06-20 06:17:19',
            ],
            [
                'id' => 11,
                'title' => 'Kebalai Dance (Rote Communal Dance)',
                'description_1' => 'Kebalai is a mass dance from Rote performed in social gatherings, including post-mourning ceremonies. It involves dancers forming circles and chanting the Manahelo or Masimba songs ',
                'description_2' => 'It strengthens social relations, unity, and emotional healing. Participants of all ages take part, demonstrating community solidarity',
                'tags' => '["Kebalai","Rote","Culture"]',
                'created_at' => '2025-06-15 10:27:06',
                'updated_at' => '2025-06-20 06:04:16',
            ],
        ]);
    }
}