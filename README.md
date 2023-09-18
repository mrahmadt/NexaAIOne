
### Suggested Names:

- **AICentral Hub**
- **UnifyAI Suite**
- **EnterpriseAISync**
- **NexaAIOne**
- **AIFusion Pro**

### Introduction:

Introducing `[Chosen Name]`, the next-generation open-source AI management platform designed for enterprises, startups, and developers alike. Experience a centralized AI REST API management platform, crafted with advanced features to empower your applications. Offers an intuitive yet powerful AI API management solution for every user, from novice developers to AI experts.


### Why You Should Use This App in Your Company/Startup/Project:

- **Production Ready**: With minimum configuration, launch a platform that's secure, swift, and scalable.
- **Developer-Friendly**: Designed with non-AI experts in mind, Offers hassle-free API integration, eliminating concerns about caching, memory management, and complex AI processes.
- **Customizable for Experts**: Tailor every API option, and even switch core components like caching databases.
- **Swift Deployment**: Enjoy compatibility across Linux, Windows, Mac OS, or deploy as a container.
- **Transparent Costs**: Crafted to minimize AI token expenses without hidden prompts or costs.
- **Application-Centric**: Minimize overheads and maximize focus on your application development, supported by REST API compatibility with all programming languages.
- **Standardized AI Configurations**: Maintain consistency across applications with a centralized AI platform.
- **Troubleshooting & Debugging**: Efficiently debug AI requests, use the "Fake LLM" AI interface, and ensure no wastage of AI tokens.
- **Versatile Deployment**: Opt for on-premises, cloud-based, or any deployment method of your choice.

### Features:

- **Memory Management**: Enhance your LLM requests with contextual memory, leveraging strategies from truncating old messages to embedding and summarizing conversations.
- **Usage Dashboard**: Gain insights into API requests, token usage per application, and more.
- **Caching Management**: Improve response times and conserve tokens with efficient caching mechanisms.
- **Ready AI Services**: Engage with AI for chats, audio, images, document/media chat, and a repository for resources.
- **Debug Mode**: Activate to monitor and inspect all your API requests for a smoother troubleshooting experience.
- **Fake LLM**: Develop and test your applications without incurring LLM-associated costs.
- **Application Authentication Management**: Secure your applications with robust authentication processes.
- **Custom APIs**: Design bespoke APIs tailored to each AI service.
- **Auto-Documentation**: Seamlessly generate comprehensive documentation for all APIs, ensuring clarity and ease-of-use for developers at every skill level.


--------
--------



# *NOT FINISHED YET*

--------

# Start with
- Check README.md to understand what is this project
* Change database to postgres
* Implement https://github.com/pgvector/pgvector (with laravel plugin available online, check if it's mature) and include it in schema and sail container

# Admin Portal
- Should we use **Filament**?

# **Users:**
- System should have default user "created by artisan"
- NO registration page!, only admin can create accounts, accounts created by admin are admin (no user levels, all admins) (from UI)
- MFA is a must for all users, admin can download his MFA recovery code
- Log user creation/deletion/login in log file
- Allow to add/edit/delete/browse

# LLM 
- Allow to add/edit/delete/browse warning if used by service (no delete)


# **API**
- check database schema
- **endpoint** is slug for the API we are creating (user enter "Hello world" --> save Hello-Word)
- **service_id** select from service table
```
$service = Service::where(['id'=>???, 'isActive'=>true ])->first();
$className = '\App\Services\\' . $service->className;

$OptionSchema = $className::getOptionSchema(service id or service Model);
```
- **options** Use the array from ```OptionSchema``` to build form for each option (, options should be grouped by the "_ group" in the array , and can expand collapse group, no need for value validation in options, example of option:
```
'systemMessage' => [
	"name" => "systemMessage",  // name of option
	"type" => "text", // type of option value (string/array/json/text/select/multiselect)
	"required" => false, // just flag if value is required or not (no validation)
	"desc" => "Provides high-level ....",  //description to show for this option
	'default' => "You are a support agent....", //default value
	"isApiOption" => false,   //default to mark it as option (developer can change it when calling api? or admin will set up the value and will not allow to change it?)
	"_group" => 'Messages', //option group name
	"options"=>[ // options if value must be selected/multi selected from this options
		'session' => 'Per Session',
		'global' => 'Global'
	]
],
```

Save options as per admin setup, UI similar to this (just an idea)

![[Pasted image 20230919002637.png]]
![[Pasted image 20230919002656.png]]

- Options can NOT be deleted or rearrange
- Allow to add/edit/delete/browse (warning if project is using this API) - admin can not edit service_id --- just other fields
## API auto generate Documentation


## Access to API
- Project id in the URL
- API id in the URL
- endPoint in the URL
- Check project token (Bearer Authentication)
* Call to APIController


# Collections
## Document Loader
### Html
### CSV
### Excel?
### PDF / with image OCR?
### Doc
### PPT?
### JSON?
### Text
### Database records? how?
### Images?
### Audio?
### Video?


# **Project:**
* Project Name, Description, Owner (string), active/inactive, token
- Should have multiple APIs (many to many)
- Generate token / allow to regenerate token
- Allow to create/edit/delete/browse


# Usage



## Debug
Debug return, json, or database?



# Services
## OpenAIChatCompletionService
- Stream in OpenAI
## OpenAITranscriptionService
## OpenAITranslationService
## Chat with Online Data
## Chat with Collection
- Add URL(s), Files(s) API
- Replace API 
- Delete API
- Browse online edit/add/replace/delete
## Classify something Media/Text
## Summarize something



# Memory 
- 'embeddings' => 'Embeddings',


# Testing Units



# Example Clients for APIs
|Use Case|Service|
|---|---|
|Internal HR Bot|Chat + Media + Voice|
|Internal Support|Chat + Media + Voice|
|Web Bot|Chat + Media + Voice|
|Assistant|Chat + Media + Voice|
|Twitter Assistant|Chat + Media + Voice|

# Articles
https://ahmadrosid.com/blog/laravel-openai-embedding
https://platform.openai.com/docs/api-reference/introduction
https://huggingface.co/blog/getting-started-with-embeddings