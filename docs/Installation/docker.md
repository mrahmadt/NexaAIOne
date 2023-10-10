# NexaAIOne Docker Installation Guide

## Introduction

This guide provides detailed instructions on deploying NexaAIOne using Docker containers. Using Docker, we ensure a quick and straightforward setup process. This deployment method will set up three containers:

1. NexaAIOne container
2. Redis Server container
3. Postgres Database container with ([Pgvector](https://github.com/pgvector/pgvector)) extension.

## Prerequisites

Before beginning the installation, ensure you have Docker and Docker-Compose installed on your system.

## Step 1: Clone the NexaAIOne-Docker Repository

First, clone the NexaAIOne-Docker repository to your desired location:

```bash
git clone https://github.com/mrahmadt/NexaAIOne-Docker.git
```


## Step 2: Change Directory

Navigate into the directory of the cloned repository:

```bash
cd NexaAIOne-Docker
```


## Step 3: Initial Installation

Run the installation script to set up the necessary Docker containers:

```bash
./install.sh
```

## Step 4: Edit Configuration File

Open the `docker/NexaAIOne/.env` file using your preferred text editor and configure it according to your requirements. Make sure to follow the inline comments provided within the file for guidance on each setting.

## Step 5: Run Installation Script Again

Execute the installation script once more to start the NexaAIOne deployment:

```bash
./install.sh
```



## Step 6: Set Up the Database

When prompted by the script:
```
Would you like to create the database? (Y/n)
```

Answer with "Y" to proceed with creating the database.


## Step 7: Create an Admin User

Upon successful database setup, the script will ask:
```
Would you like to create an admin user? (Y/n)
```

Again, answer with "Y" to create an admin user for your NexaAIOne installation.



## Step 8: Access Admin Portal

You can now log in to the Admin portal by navigating to [https://localhost/admin](https://localhost/admin).


---

Congratulations, you've successfully deployed NexaAIOne manually! If you encounter any issues, feel free to open an ticket https://github.com/mrahmadt/NexaAIOne/issues
