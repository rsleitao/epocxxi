# Enviar o projeto para o GitHub

O repositório Git local já está criado e o primeiro commit foi feito.

## 1. Criar o repositório no GitHub

1. Abre **https://github.com/new**
2. Em **Repository name** escreve: `epocxxi`
3. Escolhe **Public** (ou Private se preferires)
4. **Não** marques "Add a README file" (o projeto já tem ficheiros)
5. Clica em **Create repository**

## 2. Ligar o projeto ao repositório e fazer push

No terminal, dentro da pasta do projeto (`epocxxi`), corre os comandos abaixo.  
**Substitui `TEU_UTILIZADOR` pelo teu nome de utilizador do GitHub.**

```bash
cd c:\Users\renato\Desktop\Ajuda\code\XAMP\htdocs\epocxxi

git remote add origin https://github.com/rsleitao/epocxxi.git
git push -u origin main
```

Se o GitHub pedir autenticação:
- **Username:** o teu utilizador do GitHub
- **Password:** usa um **Personal Access Token** (o GitHub já não aceita password normal). Cria um em: GitHub → Settings → Developer settings → Personal access tokens.

## Resumo

| O que já está feito | O que falta fazer |
|--------------------|-------------------|
| `git init`         | Criar repo no GitHub (passo 1) |
| `git add .`        | `git remote add origin ...` (passo 2) |
| `git commit`       | `git push -u origin main` (passo 2) |
| Branch `main`      | |

Depois do primeiro push, para enviar alterações futuras:

```bash
git add .
git commit -m "Descrição das alterações"
git push
```
