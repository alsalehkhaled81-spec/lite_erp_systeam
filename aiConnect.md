# AI Provider Integration Report

## Project: simple-chat-app

---

## 1. Architecture Overview

```
User Browser (Vue 3)
    │
    │ POST /chat/send (FormData: message, files[], history, _token)
    ▼
ChatController (app/Http/Controllers/ChatController.php)
    │
    │ Validates request, processes files
    ▼
AiService (app/Services/AiService.php)
    │
    │ Builds OpenAI-compatible payload
    │ Sends POST to LiteLLM proxy via HTTP
    ▼
LiteLLM Proxy (api.abdalgani.com)
    │
    │ Routes to target model provider
    ▼
Model Provider (Google Gemini / NVIDIA / etc.)
```

---

## 2. Environment Configuration (.env)

```
AI_API_URL=https://api.abdalgani.com/openai/deployments/gemini-3-flash-preview/chat/completions
AI_API_KEY=sk-cNcVoQXSKw-AsDOulzw6OA
AI_MODEL=gemini-3-flash-preview
```

---

## 3. Config File (config/ai.php)

```php
return [
    'api_url'     => env('AI_API_URL'),
    'api_key'     => env('AI_API_KEY'),
    'model'       => env('AI_MODEL', 'gemini-3-flash-preview'),
    'max_tokens'  => env('AI_MAX_TOKENS', 4096),
    'temperature' => env('AI_TEMPERATURE', 0.7),
];
```

| Key | Env Var | Default | Description |
|-----|---------|---------|-------------|
| `api_url` | `AI_API_URL` | none | LiteLLM proxy endpoint URL |
| `api_key` | `AI_API_KEY` | none | API key sent as `x-litellm-api-key` header |
| `model` | `AI_MODEL` | `gemini-3-flash-preview` | Model identifier for LiteLLM routing |
| `max_tokens` | `AI_MAX_TOKENS` | `4096` | Max tokens in response |
| `temperature` | `AI_TEMPERATURE` | `0.7` | Sampling temperature |

---

## 4. API Communication Details

### Request Format

- **Method**: `POST`
- **URL**: Value of `AI_API_URL`
- **Headers**:
  - `x-litellm-api-key: <AI_API_KEY>`
  - `Content-Type: application/json`

### Payload Structure

```json
{
    "model": "gemini-3-flash-preview",
    "messages": [
        {
            "role": "user",
            "content": "Hello"
        }
    ],
    "max_tokens": 4096,
    "temperature": 0.7
}
```

### Multimodal Payload (with files)

```json
{
    "model": "gemini-3-flash-preview",
    "messages": [
        {
            "role": "user",
            "content": [
                { "type": "text", "text": "Describe this image" },
                {
                    "type": "image_url",
                    "image_url": {
                        "url": "data:image/png;base64,iVBOR..."
                    }
                }
            ]
        }
    ],
    "max_tokens": 4096,
    "temperature": 0.7
}
```

### Response Format (OpenAI-compatible)

```json
{
    "choices": [
        {
            "message": {
                "content": "AI response text"
            }
        }
    ]
}
```

Extracted via: `$response->json('choices.0.message.content')`

---

## 5. File Handling Pipeline

### Frontend (chat.blade.php)

- File input accepts: `image/*,.pdf,.doc,.docx,.txt,.csv,.json,.xml`
- Files sent as `files[]` in FormData
- Images get preview thumbnails via `FileReader.readAsDataURL()`

### Backend Processing (AiService)

| File Type | MIME Detection | API Format | Notes |
|-----------|---------------|------------|-------|
| Images (`image/*`) | Auto-detected | `image_url` with base64 data URI | Directly supported by Gemini |
| Audio (`audio/*`) | Extension-based correction (`resolveMimeType`) | `image_url` with base64 data URI | `.weba` corrected from `video/webm` to `audio/webm` |
| Video (`video/*`) | Auto-detected | `image_url` with base64 data URI | Gemini native video support |
| Text files (`text/*`, `json`, `xml`, `js`, `yaml`) | Auto-detected | Inline `text` part | Content decoded and embedded as text |
| Other files | `application/octet-stream` | Inline `text` part (base64 decoded) | Fallback to text representation |

### Extension-to-MIME Mapping

| Extension | MIME Type |
|-----------|-----------|
| `.weba` | `audio/webm` |
| `.ogg` / `.oga` | `audio/ogg` |
| `.m4a` | `audio/mp4` |
| `.mp3` | `audio/mpeg` |
| `.wav` | `audio/wav` |
| `.flac` | `audio/flac` |
| `.aac` | `audio/aac` |

---

## 6. Routes

| Method | URI | Controller@Method | Middleware |
|--------|-----|-------------------|------------|
| GET | `/` | `ChatController@index` | web |
| POST | `/chat/send` | `ChatController@send` | web |

---

## 7. Request Flow (ChatController@send)

```
1. Receive request
2. Decode history JSON string if multipart/form-data
3. Validate: message (required_without:files), files (max:5, max:10MB each), history (array)
4. Process uploaded files via AiService::processUploadedFile()
5. Build user message with files
6. Merge history + user message
7. Call AiService::chat() with all messages
8. Return JSON { success: true, message: "..." }
```

---

## 8. Available Models (from LiteLLM proxy)

### Gemini Models (Google)

| Model ID | Category |
|----------|----------|
| `gemini-3-flash-preview` | **Current default** - Fast, preview |
| `gemini-3.1-pro` | Pro - Latest generation |
| `gemini-3.1-flash` | Flash - Latest generation |
| `gemini-3.1-pro-preview` | Pro preview |
| `gemini-3.1-flash-lite-preview` | Flash lite preview |
| `gemini-3.1-flash-image-preview` | Flash with image generation |
| `gemini-3-pro-image-preview` | Pro image generation |
| `gemini-3.1-flash-live-preview` | Flash live/streaming |
| `gemini-2.5-flash` | Flash - Previous generation |
| `gemini-2.5-pro` | Pro - Previous generation |
| `gemini-2.5-flash-image` | Flash image gen - Previous gen |
| `gemini-pro-latest` | Latest pro |
| `gemini-flash-latest` | Latest flash |
| `gemini-flash-lite-latest` | Latest flash lite |

### Media Generation Models

| Model ID | Type |
|----------|------|
| `veo-3.1-generate-preview` | Video generation |
| `lyria-3-pro-preview` | Music generation |
| `nvidia/stable-diffusion-3` | Image generation |
| `flux.2-klein-4b` | Image generation |

### GLM Models (Zhipu AI)

| Model ID | Category |
|----------|----------|
| `glm-5.1` / `zai/glm-5.1` | Latest GLM |
| `glm-5-turbo` / `zai/glm-5-turbo` | Fast GLM |
| `glm-4.7` / `zai/glm-4.7` | Previous GLM |
| `glm-4.5-air` / `zai/glm-4.5-air` | Lightweight GLM |
| `glm-5:cloud` | Cloud-hosted |
| `glm-4.7:cloud` | Cloud-hosted |
| `glm-5.1:cloud` | Cloud-hosted |
| `nvidia/glm-5` | NVIDIA-hosted |
| `nvidia/glm-5.1` | NVIDIA-hosted |
| `nvidia/glm-4.7` | NVIDIA-hosted |

### DeepSeek Models

| Model ID | Category |
|----------|----------|
| `nvidia/deepseek-v4-pro` | Pro - Latest |
| `nvidia/deepseek-v4-flash` | Flash - Latest |
| `deepseek-v4-pro:cloud` | Cloud-hosted |
| `deepseek-v4-flash:cloud` | Cloud-hosted |
| `deepseek-v3.2:cloud` | Previous gen cloud |
| `nvidia/deepseek-r1` | Reasoning model |

### Qwen Models (Alibaba)

| Model ID | Category |
|----------|----------|
| `qwen3.5` | Latest Qwen |
| `qwen` | Base Qwen |
| `qwen/qwen3.5-397b-a17b` | 397B MoE |
| `nvidia/qwen3.5-397b` | NVIDIA-hosted 397B |
| `nvidia/qwen3.5-122b` | NVIDIA-hosted 122B |
| `nvidia/qwen3-coder-480b` | Coder 480B |
| `nvidia/qwq-32b` | Reasoning 32B |
| `nvidia/qwen3-next-thinking` | Thinking model |

### Kimi Models (Moonshot AI)

| Model ID | Category |
|----------|----------|
| `kimi-k2.5:cloud` | Cloud-hosted |
| `kimi-k2.6:cloud` | Cloud-hosted |
| `nvidia/kimi-k2.5` | NVIDIA-hosted |

### MiniMax Models

| Model ID | Category |
|----------|----------|
| `minimax-m2.7:cloud` | Cloud-hosted |
| `minimaxai/minimax-m2.5` | Direct |
| `nvidia/minimax-m2.7` | NVIDIA-hosted |

### Nemotron Models (NVIDIA)

| Model ID | Category |
|----------|----------|
| `nvidia/nemotron-ultra-253b` | Ultra 253B |
| `nvidia/nemotron-super-49b` | Super 49B |
| `nvidia/nemotron-3-super-120b-a12b` | Super 120B MoE |
| `nvidia/gpt-oss-120b` | GPT OSS 120B |

### Other Models

| Model ID | Category |
|----------|----------|
| `gemma4` | Google Gemma 4 |
| `gemma-4-31b-it` | Gemma 4 31B instruct |
| `nvidia/gemma-4-31b-it` | NVIDIA-hosted Gemma 4 |
| `nvidia/step-3.5-flash` | Step 3.5 Flash |

---

## 9. Known Issues & Limitations

### Current Issues (Fixed)

| Issue | Root Cause | Fix |
|-------|-----------|-----|
| CSRF token mismatch on file upload | Token not included in FormData | Added `_token` to FormData + CSRF meta tag |
| `Undefined constant "file"` | Blade interpreting Vue `{{ }}` as PHP | Changed to `@{{ }}` for Vue variables |
| `history` validation error | JSON string not decoded before validation | Added `json_decode` in controller |
| 400 error on audio files | `.weba` detected as `video/webm` instead of `audio/webm` | Added `resolveMimeType` with extension mapping |
| 500 error on non-media files | Invalid `type: 'file'` in API payload | Split handling: media → `image_url`, text → inline |

### Current Limitations

| Limitation | Details |
|-----------|---------|
| No streaming | Full response awaited before displaying |
| No model selection | Hardcoded to single model from `.env` |
| No conversation persistence | Messages lost on page refresh |
| File size limit | 10MB per file, max 5 files |
| No image generation | Media gen models not wired up |
| No token counting | `max_tokens` not tracked/limited per conversation |
| No retry logic | Single API call, no exponential backoff |
| Error details lost | Generic error messages to frontend |

---

## 10. Model Capabilities Matrix

| Capability | Recommended Models |
|-----------|-------------------|
| **Text chat** | `gemini-3-flash-preview`, `glm-5.1`, `deepseek-v4-flash` |
| **Image understanding** | `gemini-3.1-flash-image-preview`, `gemini-3-flash-preview` |
| **Audio understanding** | `gemini-3-flash-preview`, `gemini-3.1-pro` |
| **Video understanding** | `gemini-3.1-pro`, `gemini-3-flash-preview` |
| **Code generation** | `nvidia/qwen3-coder-480b`, `deepseek-v4-pro` |
| **Reasoning** | `nvidia/deepseek-r1`, `nvidia/qwen3-next-thinking`, `nvidia/qwq-32b` |
| **Fast responses** | `gemini-3-flash-preview`, `glm-5-turbo`, `deepseek-v4-flash` |
| **Image generation** | `gemini-3.1-flash-image-preview`, `flux.2-klein-4b` |
| **Video generation** | `veo-3.1-generate-preview` |
| **Music generation** | `lyria-3-pro-preview` |

---

## 11. Suggested Improvements

### High Priority
1. **Add model selection dropdown** - Allow users to pick from available models
2. **Add streaming (SSE)** - Use Server-Sent Events for token-by-token responses
3. **Conversation persistence** - Store messages in database

### Medium Priority
4. **Rate limiting** - Prevent API abuse
5. **Token usage tracking** - Monitor costs per user/session
6. **Image generation** - Wire up `gemini-3.1-flash-image-preview` or `flux.2-klein-4b`
7. **Better error handling** - Return specific error types, retry on transient failures

### Low Priority
8. **Markdown rendering** - Render AI responses as formatted markdown
9. **Conversation export** - Download chat history
10. **System prompt configuration** - Allow custom system prompts per session
