# 🧞‍♂️ **NexaAIOne**

Welcome to **NexaAIOne**, a centralized RESTful API hub for Artificial intelligence (AI). Designed for every developer. **NexaAIOne** platform brings advanced features and customizability right to your fingertips.

- [What is **NexaAIOne**?](https://github.com/mrahmadt/NexaAIOne#what-is-nexaaione)
- [Features](https://github.com/mrahmadt/NexaAIOne#features)
- [Supported AI Services](https://github.com/mrahmadt/NexaAIOne#supported-ai-services)
- [Installation & Getting Started](https://github.com/mrahmadt/NexaAIOne#documentation--getting-started)


# What is **NexaAIOne**?
In simple terms, NexaAIOne is a wrapper for OpenAI API that adds multiple essential capabilities, such as Memory, Caching, Document Q&A, and more.


**Basic** example for using NexaAIOne via API from your application (check [documentation](https://github.com/mrahmadt/NexaAIOne#documentation--getting-started) for more):
```bash
curl https://localhost/api/v1/app/1/1/chatgpt \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $AUTH_TOKEN" \
    -d '{
    "cachingPeriod": 60, --> cache AI answer for 60 minutes
    "session": "user-1397", --> Define unique session ID for every user to have different memory & cache management 
    "fakeLLM": 0, --> If you would like to use fakeLLM (during development & testing), or you want this request to be routed to OpenAI
    "enableMemory": "shortMemory", --> Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.
    "memoryOptimization": "summarization", --> Which memory management method you want to use (noOptimization, truncate, or summarization)
    "collection_id": 33, --> Use documents from collection id 33 to answer user question
    "userMessage": "How can I subscribe to your service?" --> send user question to NexaAIOne
}'
```

### High Level Design of NexaAIOne

<img src="https://github.com/mrahmadt/NexaAIOne/blob/main/docs/images/HL-Design.png?raw=true">


# Features:
- **RESTful API** All AI services are configured to be consumed as RESTful API, this way you can use them in any application you want.
- **Memory Management**: Enhance your LLM requests with contextual memory, leveraging strategies from truncating old messages to embedding and summarizing conversations.
- **Collections (Retrieval-augmented generation (RAG))**: create your own AI chat that answers from your own enterprise documents.
- **Track Usage**: Gain insights into API requests, token usage per application, and more.
- **Caching Management**: Improve response times and conserve tokens with efficient caching mechanisms.
- **Ready AI Services**: Engage with AI for chats, audio, images, document chat.
- **Developing & Testing**: Efficiently debug AI requests, use the "Fake LLM" AI interface, and ensure no wastage of AI tokens.
- **Troubleshooting & Debugging**: Monitor and inspect all your API requests for a smoother troubleshooting experience.
- **Application Authentication Management**: Secure your applications with robust authentication processes.
- **Custom APIs**: Design bespoke APIs tailored to each AI service.
- **Auto-API-Documentation**: Seamlessly generates comprehensive documentation for all APIs, ensuring clarity and ease of use for developers at every skill level.
- **Transparent Costs**: Crafted to minimize AI token expenses without hidden prompts or costs.
- **Swift Deployment**: Enjoy compatibility across Linux, Windows, Mac OS, or deploy as a container.


# Supported AI Services
- OpenAI ChatCompletion: Creates a model response for the given chat conversation.
- OpenAI Transcription: Transcribes audio into the input language.
- OpenAI Auto Translation: Translates audio into English.
- OpenAI DALL·E: an AI system that can create realistic images and art from a description in natural language.
- Microsoft Azure OpenAI
- TranslateGPT
- Text Classification
- Summarize Text
- Sentiment analysis
- Support Agent able to search knowledge base and suggest opening ticket if now answer found
- Chat with your Documents (Create Chatbot Agent for Sales,Support,HR...etc)


# Documentation & Getting Started:

## Installation
- [Docker Installation Guide](https://github.com/mrahmadt/NexaAIOne/blob/main/docs/Installation/docker.md)
- [Manual Installation Guide](https://github.com/mrahmadt/NexaAIOne/blob/main/docs/Installation/Manual_Installation.md)


## Getting Started
- [Core Components](https://github.com/mrahmadt/NexaAIOne/blob/main/docs/Getting_Started/Core_Components.md)
- [Youtube: Harnessing the Power of AI with NexaAIOne](https://www.youtube.com/watch?v=tzAxDwR12V4)
[![Youtube: Harnessing the Power of AI with NexaAIOne](http://img.youtube.com/vi/tzAxDwR12V4/0.jpg)](https://www.youtube.com/watch?v=tzAxDwR12V4 "Youtube: Harnessing the Power of AI with NexaAIOne")



Feel free to contribute, suggest features, or join us on this journey to making AI accessible and efficient for all developers.
