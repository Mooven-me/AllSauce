# AGENTS.md

This is a Symfony project. Check `composer.json` for the exact Symfony/PHP version
in use, and read `symfony.lock` to see which recipes ran. Don't assume Doctrine,
Twig, API Platform, Messenger, or Lock are installed unless one of those says so.

## Ask before generating

If the task doesn't specify, ask rather than guess:

- Persistence: Doctrine ORM, Doctrine ODM, or none?
- Interface: server-rendered (Twig), API (Serializer, maybe API Platform), or both?
- Auth: SecurityBundle, and which authenticator?

If you can't ask (no interactive channel), state the assumption you're making and
pick the smallest option (e.g. no persistence layer) rather than scaffolding a
full stack nobody asked for.

## Adding features: Flex, not hand-wiring

Install new capabilities with `composer require <package>` (e.g. `symfony/lock`,
`symfony/messenger`, `orm-pack`) and let the Flex recipe register the bundle and
generate its config. Don't hand-edit `config/bundles.php` or hand-write a bundle's
base config; that's what the recipe is for. Don't skip a good-fit component just
because it isn't installed yet; installing it is one command.

## Conventions

Follow https://symfony.com/doc/current/best_practices.html to write idiomatic
Symfony:

- Use PHP attributes for framework metadata, and not only on controllers:
  `#[Route]`, `#[MapRequestPayload]`, `#[IsGranted]` on actions, `#[Assert\...]`
  on properties, `#[AsCommand]`, `#[AsEventListener]`, `#[AsMessageHandler]`, and
  `#[AsAlias]` / `#[AsTaggedItem]` / `#[Autoconfigure]` on services. No YAML or
  XML routing.
- Rely on autowiring and autoconfiguration. Type-hint constructor arguments and
  let the container resolve them. Where a type-hint can't express it, stay in the
  class with `#[Autowire]` (parameters, env vars, expressions) or `#[Target]` (one
  of several implementations of an interface). A YAML service definition is the
  last resort, not the first.
- Controllers extend `AbstractController`, stay thin, and delegate to services.
- Use the framework for what it already does: Form for server-rendered forms,
  Validator for validation, Serializer for JSON, Messenger for async work,
  Security (voters, authenticators) for access control, Twig `path()`/`url()`
  instead of hardcoded URLs.
- Before hand-writing infrastructure (locks, queues, caches, HTTP clients,
  mailers, schedulers) or reaching for a third-party library, check whether a
  Symfony component covers it. It usually does.

Three specifics worth spelling out, because they are easy to get wrong:

- Bind request data with `#[MapRequestPayload]` / `#[MapQueryString]` on action
  arguments, which wires up Serializer and Validator for you, instead of calling
  `json_decode()` or `SerializerInterface` by hand. If neither package is
  installed yet, `composer require` them rather than falling back to manual
  parsing.
- Use constructor property promotion, and `readonly` for DTOs and value objects.
  Don't mark a service `readonly` if it might become `lazy: true`: a lazy proxy
  can't extend a `readonly` class.
- Use `symfony/lock` (`LockFactory`) for mutual exclusion. A hand-built flag or
  lock file looks fine in review and is usually wrong under concurrency.

## Everyday workflow

- Run the app with `symfony serve -d`, and commands with `symfony console ...`
  (or `bin/console` when the Symfony CLI isn't available).
- When something fails, read `var/log/dev.log` and the web profiler
  (`/_profiler`) before changing code.
- If `maker-bundle` is installed, prefer `bin/console make:*` with every argument
  passed up front and `--no-interaction` where supported: makers prompt on a
  terminal by default, which hangs a non-interactive shell. If a maker still
  needs interactive input, hand-write the code instead.
- If Doctrine ORM is installed, schema changes go through migrations
  (`bin/console make:migration`, then `doctrine:migrations:migrate`), never
  `doctrine:schema:update` or hand-written SQL.
- `.env` is committed and holds defaults only. Real secrets belong in `.env.local`
  (git-ignored) or the secrets vault (`bin/console secrets:set`), read via
  `%env(...)%`.

## Testing

Install `symfony/test-pack` if it isn't already. Functional/HTTP tests extend
`WebTestCase`; service-level tests extend `KernelTestCase`. Run
`php bin/phpunit` (falls back to `vendor/bin/phpunit`). A feature isn't done
until it has a test that exercises it the way a caller would, an HTTP request for
a controller or a service call for a service, not just "it didn't throw."

## Code style

Symfony's coding standard, the `@Symfony` php-cs-fixer ruleset (a PSR-12-derived
superset). Run `vendor/bin/php-cs-fixer fix` if `friendsofphp/php-cs-fixer` is
installed; it isn't part of the skeleton by default.

## Discover, don't guess

Framework APIs change between versions and your training data may be stale. Look
things up in the project instead of relying on memory:

- `bin/console about`: versions, environment, paths.
- `bin/console debug:router`, `debug:container`, `debug:autowiring <name>`,
  `debug:config <bundle>`, `config:dump-reference <bundle>`: what exists and how
  it is configured.
- `bin/console lint:container`, plus `lint:twig templates/` and
  `lint:yaml config/` where those packages are installed: validate before running.
- Read the installed source and docblocks under `vendor/`.
- Docs: https://symfony.com/doc/current/ (switch to the version matching
  `composer.json` if it differs).

## Project Architecture & Domain ("Sauce" Quiz App)

### Domain Concept: What is a "Sauce"?
- **Not culinary**: In this project, a **"Sauce"** is a quiz game topic / theme / mode (e.g. `LoLSauce` for League of Legends, `BlankSauce` as a default). Never treat sauces as food or recipes.
- **`Sauce` entity (`App\Entity\Sauce`)**: Represents a quiz topic with `name`, `slug` (URL identifier), and `service` (`SauceServiceEnum`).
- **`SauceRoom` entity (`App\Entity\SauceRoom`)**: A multiplayer match room with a unique 5-character `roomCode` (e.g. `K7PX9`), a `leader` (`User`), connected `users` (Collection of `User`), and a linked `Sauce`.
- **`User` entity (`App\Entity\User`)**: Players identify with a UUID v7. Users default to `anonymous = true`, and can later link an email/password. Tracks `score` and the active `sauceRoom`.

### Technical Architecture
- **Backend**: Symfony 8.1 / PHP 8.5+ with Doctrine ORM (PostgreSQL), Symfony Messenger (async queues via `doctrine_messenger` / PostgreSQL `LISTEN/NOTIFY`), Symfony Lock, and Mercure SSE for real-time multiplayer updates.
- **Frontend**: React 19 Single Page Application rendered via Pentatrion ViteBundle (`templates/base/index.html.twig` -> `assets/main.jsx`).
- **Authentication**: Stateless JWT + refresh tokens stored in httpOnly cookies (`lexik/jwt-authentication-bundle` + `gesdinet/jwt-refresh-token-bundle`). Handled by `JwtCookieAuthenticatorService` and frontend `sendData.jsx` wrapper with transparent 401 refresh.

### Dynamic Theming & Sauce Services
- Each sauce has an implementation of `SauceServiceInterface` (e.g. `LoLSauceService`), autowired via `SauceServiceEnum`.
- The service maps to a `SauceCssEnum` (`LoLSauceCss`), which defines structured CSS rules (colors, animated background images, buttons).
- Endpoint `GET /api/v1/sauce/{slug}/css` serves these rules, and `SauceDispatcher.jsx` dynamically injects them into the document `<head>` when entering a sauce.

### Real-Time Multiplayer Lifecycle (Mercure + Messenger)
1. **Creation / Join**: User enters pseudo at `/:slug` (`Lobby.jsx`). Creating a room triggers `POST /api/v1/sauce/{slug}/create_room`, handled by `RoomUtilManager::createRoom` (generates 5-char code, sets leader, redirects to `/:slug/:roomCode`).
2. **Channel Subscription**: `RoomDispatcher.jsx` connects via `MercureProvider` to SSE topic `https://subscribed.channel/room/{slug}/{roomCode}`.
3. **Game Launch**: Leader clicks start (`POST /api/v1/sauce/{slug}/start`). `RoomUtilManager::startRoom()` resets scores and dispatches `RoomUpdateMessage($roomId, MercureTypeEnum::START)`.
4. **Message Handling**: `RoomUpdateMessageHandler` uses `#[AutowireLocator(SauceMessageHandlerInterface::class)]` keyed by handler class to execute `StartMessageHandler`, which publishes the `"start"` event to the Mercure room topic.

### Dynamic JSON Schema Engine (`Formable`)
- Entity classes tagged with `#[Formable(path: '...')]` (e.g. `Sauce`) and property tag `#[NotFormable]` are indexed by `FormableRegistry`.
- `AutoSchemaBuilder` reflects the entity and serves JSON Schema at `GET /api/v1/form/{path}`.
- The frontend renders dynamic modals (`DynamicSchemaModal.jsx`) via `@rjsf/core` without hand-crafting form fields.

### Key Directory Map
- `src/Entity/`: `Sauce.php`, `SauceRoom.php`, `User.php`, `RefreshToken.php`.
- `src/Controller/V1/`: `AuthController.php`, `SaucesController.php`, `SauceController.php`, `FormController.php`.
- `src/Enum/`: `SauceServiceEnum.php`, `SauceCssEnum.php`, `MercureTypeEnum.php`.
- `src/Service/Room/`: `RoomUtilManager.php` (room code generation, player joining/removal, match start).
- `src/Service/Sauce/`: `SauceServiceInterface.php`, `SauceServiceAbstract.php`, `LoLSauceService.php`, `BlankSauceService.php`.
- `src/Sauce/Css/`: `CssInterface.php`, `LoLSauceCss.php`, `BlankCss.php`.
- `src/Message/` & `src/MessageHandler/`: `RoomUpdateMessage.php`, `RoomUpdateMessageHandler.php`.
- `src/Service/SauceMessage/`: `SauceMessageHandlerInterface.php`, `StartMessageHandler.php`.
- `assets/components/`:
  - `Index/Sauces.jsx`: Grid of available sauces.
  - `Sauce/Scenes/Lobby.jsx`: Room creation/joining interface.
  - `Sauce/Scenes/RoomDispatcher.jsx`: Mercure room wrapper.
  - `Sauce/Scenes/Room.jsx`: Waiting room lobby.
  - `Utils/sendData.jsx`: Authenticated fetch wrapper with automatic token refresh.
  - `Utils/Form/`: `useDynamicForm.jsx` & `DynamicSchemaModal.jsx` (RJSF form integration).
