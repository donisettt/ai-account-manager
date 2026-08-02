<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'nama' => 'Antigravity',
                'status_aktif' => true,
                'keterangan' => 'AI development tool dengan fitur anti-gravity untuk coding yang lebih efisien',
            ],
            [
                'nama' => 'Kiro',
                'status_aktif' => true,
                'keterangan' => 'AI-powered development environment untuk modern development',
            ],
            [
                'nama' => 'Cuana',
                'status_aktif' => true,
                'keterangan' => 'AI assistant untuk analisis dan automasi',
            ],
            [
                'nama' => 'Claude',
                'status_aktif' => true,
                'keterangan' => 'AI assistant dari Anthropic untuk berbagai keperluan',
            ],
            [
                'nama' => 'Gemini',
                'status_aktif' => true,
                'keterangan' => 'AI model dari Google dengan kemampuan multimodal',
            ],
            [
                'nama' => 'Cursor',
                'status_aktif' => true,
                'keterangan' => 'AI-powered code editor untuk produktivitas developer',
            ],
            [
                'nama' => 'Windsurf',
                'status_aktif' => true,
                'keterangan' => 'AI development tool dengan interface modern',
            ],
            [
                'nama' => 'ChatGPT',
                'status_aktif' => true,
                'keterangan' => 'AI chatbot dari OpenAI untuk berbagai keperluan',
            ],
            [
                'nama' => 'Bolt',
                'status_aktif' => true,
                'keterangan' => 'Fast AI development tool',
            ],
            [
                'nama' => 'Lovable',
                'status_aktif' => true,
                'keterangan' => 'AI tool untuk design dan development',
            ],
        ];

        foreach ($tools as $tool) {
            Tool::create($tool);
        }
    }
}
