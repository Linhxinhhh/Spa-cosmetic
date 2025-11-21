<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nhà cung cấp AI mặc định
    |--------------------------------------------------------------------------
    | openai | gemini | ollama (local)
    */
    'provider' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Cấu hình từng nhà cung cấp
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'openai' => [
            'api_key'      => env('OPENAI_API_KEY'),
            'base_url'     => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'chat_model'   => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
            'embed_model'  => env('OPENAI_EMBED_MODEL', 'text-embedding-3-large'),
            'temperature'  => (float) env('OPENAI_TEMPERATURE', 0.3),
            'max_tokens'   => (int) env('OPENAI_MAX_TOKENS', 1024),
            'stream'       => (bool) env('OPENAI_STREAM', true),
            'timeout'      => (int) env('OPENAI_TIMEOUT', 30),
        ],

        'gemini' => [
            'api_key'      => env('GEMINI_API_KEY'),
            'base_url'     => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'chat_model'   => env('GEMINI_MODEL', 'gemini-1.5-pro'),
            'temperature'  => (float) env('GEMINI_TEMPERATURE', 0.3),
            'max_tokens'   => (int) env('GEMINI_MAX_TOKENS', 1024),
            'stream'       => (bool) env('GEMINI_STREAM', true),
            'timeout'      => (int) env('GEMINI_TIMEOUT', 30),
        ],

        // Tùy chọn nếu bạn chạy mô hình cục bộ bằng Ollama
        'ollama' => [
            'base_url'     => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'chat_model'   => env('OLLAMA_MODEL', 'llama3.1'),
            'temperature'  => (float) env('OLLAMA_TEMPERATURE', 0.3),
            'timeout'      => (int) env('OLLAMA_TIMEOUT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mặc định chung cho chatbot
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'lang'              => env('AI_LANG', 'vi'),
        'system_prompt'     => env('AI_SYSTEM_PROMPT',
            "Bạn là trợ lý AI của Lyn & Spa. Hỗ trợ tư vấn mỹ phẩm, dịch vụ spa, gợi ý lộ trình chăm sóc da, " .
            "đặt lịch và tra cứu đơn hàng. Luôn thân thiện, ngắn gọn, ưu tiên tiếng Việt."
        ),
        'max_history'       => (int) env('AI_MAX_HISTORY', 8),        // số message lịch sử giữ trong context
        'max_context_chars' => (int) env('AI_MAX_CONTEXT', 9000),     // cắt bớt context quá dài
        'typing_delay_ms'   => (int) env('AI_TYPING_DELAY', 0),       // 0 = tắt
    ],

    /*
    |--------------------------------------------------------------------------
    | RAG / Tìm kiếm tri thức nội bộ
    |--------------------------------------------------------------------------
    | Dùng MySQL FULLTEXT (kb_chunks.content) như đã tạo migration.
    | Nếu sau này dùng vector DB, bật driver 'vector' và cấu hình bên dưới.
    */
    'retrieval' => [
        'enabled'       => (bool) env('AI_RAG_ENABLED', true),
        'driver'        => env('AI_RAG_DRIVER', 'mysql_fulltext'),  // mysql_fulltext | vector

        'mysql_fulltext' => [
            'table'         => 'kb_chunks',
            'column'        => 'content',
            'limit'         => (int) env('AI_RAG_LIMIT', 8),
            'min_length'    => 60,
            'match_mode'    => env('AI_RAG_MATCH', 'NATURAL'),       // NATURAL | BOOLEAN
            'order_by_score'=> true,
        ],

        // Tuỳ chọn nếu dùng vector store (tự triển khai)
        'vector' => [
            'table'             => env('AI_VECTOR_TABLE', 'kb_chunks'),
            'embedding_column'  => env('AI_VECTOR_COLUMN', 'embedding'),
            'dim'               => (int) env('AI_VECTOR_DIM', 1536),
            'metric'            => env('AI_VECTOR_METRIC', 'cosine'),
            'limit'             => (int) env('AI_VECTOR_LIMIT', 8),
        ],

        'join_context_with' => "\n---\n",
        'prepend_heading'   => true,
        'max_chars'         => (int) env('AI_RAG_MAX_CHARS', 4000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Buttons gợi ý nhanh trong UI
    |--------------------------------------------------------------------------
    */
    'suggestions' => [
        'enabled' => true,
        'preset'  => [
            'Da dầu & mụn',
            'Dưới 500k',
            'Đặt lịch spa',
            'Tra đơn #',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cho phép chatbot gọi các “tool” nội bộ
    |--------------------------------------------------------------------------
    */
    'tools' => [
        'order_lookup'  => (bool) env('AI_TOOL_ORDER_LOOKUP', true), // tra đơn theo mã
        'booking'       => (bool) env('AI_TOOL_BOOKING', true),      // tạo yêu cầu đặt lịch
        'product_search'=> (bool) env('AI_TOOL_PRODUCT_SEARCH', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bảo mật & nhật ký
    |--------------------------------------------------------------------------
    */
    'security' => [
        'allowed_domains' => array_filter(explode(',', env('AI_ALLOWED_DOMAINS', ''))),
        'log_prompts'     => (bool) env('AI_LOG_PROMPTS', false), // ghi prompt/response vào DB/log
        'mask_pii'        => (bool) env('AI_MASK_PII', false),    // che số ĐT/email trong log
    ],

    /*
    |--------------------------------------------------------------------------
    | Cấu hình Knowledge Base (import)
    |--------------------------------------------------------------------------
    */
    'kb' => [
        'files_dir' => storage_path('app/ai_kb'),
        'import'    => [
            'products' => true,   // dùng lệnh: php artisan ai:kb:import --from=products
            'services' => true,   //               php artisan ai:kb:import --from=services
            'faqs'     => true,   //               php artisan ai:kb:import --from=faqs
        ],
        'chunk' => [
            'target'  => (int) env('AI_KB_CHUNK_TARGET', 900),
            'overlap' => (int) env('AI_KB_CHUNK_OVERLAP', 150),
        ],
    ],

];
