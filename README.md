## 0. Pré-requisitos (conferir antes de tudo)

No terminal:

```bash
php -v
composer -V
node -v
npm -v
```

Você precisa, no mínimo:

* **PHP ≥ 8.2**
* **Composer** instalado globalmente
* **Node.js ≥ 18** (ideal 20+)
* **npm** (vem junto com o Node)

Se isso passar, segue o jogo.

---

## 1. Criar um novo projeto Laravel 12

No diretório onde você quer o projeto:

```bash
composer create-project --prefer-dist laravel/laravel www
cd laravel-spa
```

Gerar o arquivo `.env` e a chave da aplicação:

```bash
cp .env.example .env   # no Windows, pode usar: copy .env.example .env
php artisan key:generate
```

Se quiser já testar:

```bash
php artisan serve
```

Acesse: [http://127.0.0.1:8000](http://127.0.0.1:8000)

Se aparecer a tela padrão do Laravel, está ok.

---

## 2. Configuração básica do `.env` (API + banco)

Abra o arquivo `.env` na raiz e ajuste o básico (use o banco que você tiver):

```env
APP_NAME="Laravel SPA"
APP_ENV=local
APP_KEY=gerado_pelo_artisan
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Banco de dados (exemplo MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_spa
DB_USERNAME=root
DB_PASSWORD=

# Sanctum / SPA
FRONTEND_URL=http://127.0.0.1:5173
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:5173,localhost:5173,127.0.0.1:8000,localhost:8000
SESSION_DOMAIN=127.0.0.1
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

Crie o banco (`laravel_spa`) no MySQL (phpMyAdmin, Adminer ou CLI — como preferir).

Depois rode:

```bash
php artisan migrate
```

---

## 3. Code quality PHP: Laravel Pint (já vem pronto)

O Laravel 12 já traz o **Laravel Pint** como dev-dependency. Você já pode usá-lo para formatar o código PHP.

Testar o Pint:

```bash
./vendor/bin/pint --test
```

Formatar código automaticamente:

```bash
./vendor/bin/pint
```

Se quiser um arquivo de config (opcional), gere:

```bash
./vendor/bin/pint --generate
```

Vai criar `pint.json` na raiz.

> Isso já te dá uma ferramenta profissional de estilo de código para o PHP.

Se você quiser, depois a gente adiciona **PHPStan/Larastan** também, mas vamos focar primeiro no SPA.

---

## 4. Instalar e configurar Laravel Sanctum (SPA)

### 4.1. Instalar Sanctum

No Laravel 12 ele geralmente já vem instalado, mas para garantir:

```bash
composer require laravel/sanctum
```

Se for necessário publicar as migrações (alguns setups ainda pedem):

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 4.2. Middleware do Sanctum (API SPA)

Abra `app/Http/Kernel.php` e confira se no grupo `api` já existe isso:

```php
protected $middlewareGroups = [
    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
```

Se não tiver, adicione essa linha (`EnsureFrontendRequestsAreStateful`) dentro do grupo `api`.

### 4.3. Configurar CORS

Abra `config/cors.php` e ajuste assim:

```php
return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'user',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://127.0.0.1:5173',
        'http://localhost:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ESSENCIAL para Sanctum com SPA
    'supports_credentials' => true,

];
```

### 4.4. Rotas de autenticação Sanctum para SPA

Vamos usar autenticação via sessão/cookie (SPA):

Edite `routes/api.php` e coloque esse conteúdo (pode substituir tudo por agora):

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json(['ok' => true]);
});

/**
 * Rota para obter o usuário autenticado (protegia por Sanctum)
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Login (SPA)
 */
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Credenciais inválidas.',
        ], 422);
    }

    Auth::login($user);

    return response()->json([
        'message' => 'Autenticado com sucesso.',
        'user'    => $user,
    ]);
});

/**
 * Logout (SPA)
 */
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logout realizado com sucesso.',
    ]);
});
```

> Importante: essas rotas usam cookies de sessão. O Sanctum cuida da parte de autenticação (guard `auth:sanctum`).

---

## 5. Preparar o frontend: React + Vite + TypeScript (sem starter kit)

Por padrão o Laravel já vem com Vite configurado para JS simples. Vamos instalar React + TS e ferramentas.

### 5.1. Instalar dependências JS

No diretório do projeto (`laravel-spa`):

```bash
npm install
```

Agora, instalar React + TypeScript + Vite React plugin + Axios + ESLint/Prettier:

```bash
npm install react react-dom
npm install -D typescript @types/react @types/react-dom
npm install -D @vitejs/plugin-react-swc
npm install axios

# ESLint + TypeScript + React
npm install -D eslint @typescript-eslint/parser @typescript-eslint/eslint-plugin
npm install -D eslint-plugin-react eslint-plugin-react-hooks

# (Opcional, mas profissional) Prettier + integração com ESLint
npm install -D prettier eslint-config-prettier eslint-plugin-prettier
```

---

## 6. Reorganizar estrutura JS → TS (resources/ts)

Vamos criar uma pasta para TypeScript e preparar o entrypoint do React.

### 6.1. Criar pasta `resources/ts` e limpar JS antigo

```bash
mkdir -p resources/ts
```

Se existirem `resources/js/app.js`, `resources/js/bootstrap.js` etc, você pode limpar/ignorar. Vamos centralizar tudo em `resources/ts`.

### 6.2. Criar `resources/ts/main.tsx` (componente principal)

Crie o arquivo:

`resources/ts/main.tsx`:

```tsx
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './pages/App';

const rootElement = document.getElementById('app');

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement);

    root.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>,
    );
}
```

### 6.3. Criar `resources/ts/pages/App.tsx`

`resources/ts/pages/App.tsx`:

```tsx
import React from 'react';
import { useEffect, useState } from 'react';
import { api, getUser } from '@/services/api';

interface User {
    id: number;
    name: string;
    email: string;
}

const App: React.FC = () => {
    const [user, setUser] = useState<User | null>(null);
    const [email, setEmail] = useState('admin@example.com');
    const [password, setPassword] = useState('password');
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState<string | null>(null);

    useEffect(() => {
        // Tenta carregar usuário autenticado ao abrir a página
        getUser()
            .then((u) => setUser(u))
            .catch(() => {
                setUser(null);
            });
    }, []);

    const handleLogin = async (event: React.FormEvent) => {
        event.preventDefault();
        setLoading(true);
        setMessage(null);

        try {
            await api.login({ email, password });
            const u = await getUser();
            setUser(u);
            setMessage('Login realizado com sucesso!');
        } catch (error: any) {
            console.error(error);
            setMessage(
                error?.response?.data?.message ||
                    'Erro ao autenticar, verifique as credenciais.',
            );
        } finally {
            setLoading(false);
        }
    };

    const handleLogout = async () => {
        setLoading(true);
        setMessage(null);

        try {
            await api.logout();
            setUser(null);
            setMessage('Logout realizado com sucesso!');
        } catch (error) {
            console.error(error);
            setMessage('Erro ao realizar logout.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'sans-serif' }}>
            <h1>Laravel 12 + React + Sanctum</h1>

            {message && (
                <p
                    style={{
                        padding: '8px 12px',
                        borderRadius: 4,
                        backgroundColor: '#f0f4ff',
                    }}
                >
                    {message}
                </p>
            )}

            {user ? (
                <>
                    <h2>Usuário autenticado</h2>
                    <ul>
                        <li>ID: {user.id}</li>
                        <li>Nome: {user.name}</li>
                        <li>Email: {user.email}</li>
                    </ul>

                    <button onClick={handleLogout} disabled={loading}>
                        {loading ? 'Saindo...' : 'Logout'}
                    </button>
                </>
            ) : (
                <>
                    <h2>Login</h2>
                    <form onSubmit={handleLogin}>
                        <div style={{ marginBottom: 8 }}>
                            <label>
                                Email:
                                <input
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    style={{ width: '100%' }}
                                />
                            </label>
                        </div>
                        <div style={{ marginBottom: 8 }}>
                            <label>
                                Senha:
                                <input
                                    type="password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    style={{ width: '100%' }}
                                />
                            </label>
                        </div>
                        <button type="submit" disabled={loading}>
                            {loading ? 'Entrando...' : 'Entrar'}
                        </button>
                    </form>
                </>
            )}
        </div>
    );
};

export default App;
```

> Esse componente já usa o nosso `api.ts` (vamos criar já já) e o alias `@`.

---

## 7. Configurar TypeScript (tsconfig + paths)

Crie o arquivo `tsconfig.json` na raiz do projeto com o conteúdo:

```json
{
  "compilerOptions": {
    "target": "ESNext",
    "useDefineForClassFields": true,
    "lib": ["DOM", "DOM.Iterable", "ESNext"],
    "allowJs": false,
    "skipLibCheck": true,
    "esModuleInterop": true,
    "allowSyntheticDefaultImports": true,
    "strict": true,
    "forceConsistentCasingInFileNames": true,
    "module": "ESNext",
    "moduleResolution": "Node",
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "jsx": "react-jsx",
    "baseUrl": ".",
    "paths": {
      "@/*": ["resources/ts/*"]
    }
  },
  "include": ["resources/ts"],
  "references": []
}
```

Isso habilita o alias `@` no TypeScript.

---

## 8. Configurar Vite + React + alias `@`

O Laravel 12 cria por padrão `vite.config.js`. Vamos substituí-lo por **TypeScript** (`vite.config.ts`).

### 8.1. Renomear e editar Vite config

No diretório raiz:

* Renomeie `vite.config.js` → `vite.config.ts` (ou apague o `.js` e crie o `.ts`).

Crie/edite `vite.config.ts` com esse conteúdo:

```ts
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react-swc';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/ts/main.tsx'],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/ts'),
        },
    },
});
```

> Aqui está o alias `@` apontando para `resources/ts`.

---

## 9. Blade para carregar o React (SPA)

Vamos criar uma view que apenas carrega o React.

### 9.1. Criar `resources/views/app.blade.php`

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Laravel SPA</title>
    @vite('resources/ts/main.tsx')
</head>
<body>
<div id="app"></div>
</body>
</html>
```

### 9.2. Ajustar rota web para usar essa view

Edite `routes/web.php` e deixe assim:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('{any?}', function () {
    return view('app');
})->where('any', '.*');
```

> Isso faz com que qualquer rota web (`/`, `/login`, `/dashboard`, etc.) carregue o React, comportamento comum para SPA.

---

## 10. `api.ts` – abstração Axios com Sanctum

Agora a parte importante: **Axios + Sanctum + API** com uma camada de abstração.

### 10.1. Criar `resources/ts/services/api.ts`

Crie a pasta:

```bash
mkdir -p resources/ts/services
```

Arquivo: `resources/ts/services/api.ts`:

```ts
import axios, { AxiosInstance } from 'axios';

/**
 * Cria a instância principal do Axios para a API.
 * - baseURL: sempre aponta para /api do Laravel
 * - withCredentials: necessário para cookies de sessão (Sanctum)
 * - withXSRFToken: envia automaticamente o token CSRF
 */
const http: AxiosInstance = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true,
});

/**
 * Garante que o cookie de CSRF do Sanctum foi definido.
 * Deve ser chamado antes do primeiro POST/PUT/DELETE autenticado.
 */
async function ensureCsrfCookie(): Promise<void> {
    await axios.get('/sanctum/csrf-cookie', {
        withCredentials: true,
    });
}

/**
 * Pequena função helper para tratar erros de forma centralizada (opcional).
 */
function handleError(error: unknown): never {
    // Aqui você pode fazer log, enviar para ferramenta de monitoramento, etc.
    throw error;
}

/**
 * Funções de autenticação usando Sanctum (SPA).
 */
async function login(data: { email: string; password: string }): Promise<void> {
    try {
        await ensureCsrfCookie();

        await http.post('/login', data);
    } catch (error) {
        handleError(error);
    }
}

async function logout(): Promise<void> {
    try {
        await http.post('/logout');
    } catch (error) {
        handleError(error);
    }
}

async function fetchUser<T = any>(): Promise<T> {
    try {
        const response = await http.get<T>('/user');
        return response.data;
    } catch (error) {
        handleError(error);
    }
}

/**
 * Funções genéricas para GET/POST/PUT/DELETE (opcional, mas útil).
 */
async function get<T = any>(url: string, params?: Record<string, unknown>): Promise<T> {
    try {
        const response = await http.get<T>(url, { params });
        return response.data;
    } catch (error) {
        handleError(error);
    }
}

async function post<T = any>(
    url: string,
    data?: unknown,
): Promise<T> {
    try {
        await ensureCsrfCookie();

        const response = await http.post<T>(url, data);
        return response.data;
    } catch (error) {
        handleError(error);
    }
}

export const api = {
    http,
    login,
    logout,
    get,
    post,
};

export async function getUser() {
    return fetchUser();
}
```

Agora você já tem:

* `api.login({ email, password })`
* `api.logout()`
* `getUser()` para pegar o usuário logado
* `api.get('/minha-rota')` e `api.post('/outra-rota', dados)` para uso geral.

---

## 11. Configurar ESLint + Prettier para React + TS

### 11.1. Arquivo `.eslintrc.cjs`

Na raiz do projeto, crie `.eslintrc.cjs`:

```js
module.exports = {
    root: true,
    env: {
        browser: true,
        es2021: true,
        node: true,
    },
    parser: '@typescript-eslint/parser',
    parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
        ecmaFeatures: {
            jsx: true,
        },
    },
    settings: {
        react: {
            version: 'detect',
        },
    },
    plugins: ['react', 'react-hooks', '@typescript-eslint', 'prettier'],
    extends: [
        'eslint:recommended',
        'plugin:react/recommended',
        'plugin:react-hooks/recommended',
        'plugin:@typescript-eslint/recommended',
        'plugin:prettier/recommended',
    ],
    rules: {
        // React 17+ não precisa importar React em cada arquivo
        'react/react-in-jsx-scope': 'off',
        'react/jsx-uses-react': 'off',

        // Ajustes de estilo (você pode personalizar)
        '@typescript-eslint/explicit-module-boundary-types': 'off',
        'prettier/prettier': 'warn',
    },
    ignorePatterns: ['public/build/', 'node_modules/'],
};
```

### 11.2. Arquivo `.prettierrc.json` (opcional, mas recomendado)

Crie `.prettierrc.json`:

```json
{
  "singleQuote": true,
  "trailingComma": "all",
  "printWidth": 100,
  "tabWidth": 4,
  "semi": true
}
```

### 11.3. Ajustar `package.json` (scripts)

Abra `package.json` e ajuste a parte `scripts` assim:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "lint": "eslint \"resources/ts/**/*.{ts,tsx}\""
  }
}
```

> Agora `npm run lint` vai rodar o ESLint em tudo dentro de `resources/ts`.

---

## 12. Criar um usuário de teste para login

Para testar a autenticação Sanctum, você precisa de um usuário no banco.

### 12.1. Criar seeder rápido (opcional, via tinker é mais rápido)

Forma rápida via Tinker:

```bash
php artisan tinker
```

Dentro do Tinker:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
]);
```

Digite `exit` para sair.

Esse é o usuário que configurei como default no React (`admin@example.com` / `password`).

---

## 13. Subir tudo e testar

Em **dois terminais** diferentes:

### 13.1. Backend (Laravel)

```bash
php artisan serve
# vai rodar em http://127.0.0.1:8000
```

### 13.2. Frontend (Vite)

```bash
npm run dev
# normalmente em http://127.0.0.1:5173
```

Abra no navegador: [http://127.0.0.1:5173](http://127.0.0.1:5173)

Você deverá ver:

* Tela com formulário de login.
* Ao logar com `admin@example.com` / `password`, o React chama:

    * `/sanctum/csrf-cookie`
    * `/api/login`
    * `/api/user`
* Deverá aparecer o bloco “Usuário autenticado” com ID/nome/email.

Se isso acontecer, você já tem:

* Laravel 12 instalado ✔
* Sanctum configurado com SPA + cookies ✔
* React + Vite + TypeScript funcionando ✔
* Alias `@` configurado no **Vite** e **tsconfig** ✔
* `api.ts` com abstração Axios + Sanctum ✔
* Pint para PHP + ESLint/Prettier para TS/React configurados ✔

---

## 14. Comandos que você vai usar no dia a dia

Resumo para você copiar e colar quando precisar:

```bash
# Subir Laravel
php artisan serve

# Subir Vite
npm run dev

# Build em produção
npm run build

# Lint React/TS
npm run lint

# Lint/format PHP
./vendor/bin/pint
./vendor/bin/pint --test
```
