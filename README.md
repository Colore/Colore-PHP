# Colore

To boldly flow where no-one has flown...flowed...before.

## Why!?

Because it made programming in PHP actually fun again.

Why bother with writing escalating layers of code when you can configure everything together!? That's right! Code as configuration!

Colore is the ultimate mediator! Or so I'd like to think.

Colore comes from:

-   Context: what is the context/flow/pipeline that we want to access?
-   Logic: what code do we need to execute?
-   Render: and how are we going to render this?

Colore promotes code re-use by slicing and dicing your code into nice aspects and layering the like a layer cake. But more practical.

Plus, it's made for ultimate dynamicity! (Is that even a real word!?)

It's middleware on steroids.

## How!?!

Glad you asked! You give Colore some helpers. One to make it aware of and being able to interact with the environment and another to be able to lookup contexts.

BUT WHAT DOES THIS MEAN!?

Colore is environment agnostic. This means that Colore is like a honey badger. Want to run it in ye standard Apache/PHP environment? Go for it! Want to use it to parse MQTT messages? Go for it! Want to embed it into a desktop application? Go for it! Colore doesn't care!

The request helper is initialized in the environment Colore is running in and consequently is able to talk to it.

The context helper is your gateway to your "endpoint" configuration. And you can name them anything you want. And if the standard solutions don't work. You can easily extend it!

Once the context is loaded, Colore will start to go down the stack of logic and execute everything until it's time to render!

But, wait! There is more! Do you sometimes need to execute extra logic? Just insert or append it!

You could even write some logic that will look up what logic needs to be executed as it's being executed!

Or you can just have solid lists. In plain JSON configuration files that you load at start. Or you make Colore look up contexts from REDIS or a database so you can dynamically look up and change your contexts.

Deploys and feature releases have never been easier!

## What?!

Yeah. Try the ping example and go see for yourself!

## Serious time

### Get Started

As Colore is currently still in development, it's not yet available from `composer`.

But if you want to get a feel for it, check the `examples/ping` folder or any of the other examples for some examples of minumum amount of viable code.

To run the `ping` example

-   install the dependencies with `composer install`
-   run the `./docker-ping-example.sh`
-   open the example on `http://localhost:8765/`

### Concepts

Colore's flow is compartmentalized and can be broken down into the following aspects:

| Role                       | Function                                                                                                                                                                                                                                                                      |
| -------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Execution Environment (EE) | E.g. Apache with mod_php                                                                                                                                                                                                                                                      |
| Entrypoint                 | This is the code that provides initial execution and bootstrapping the Engine.                                                                                                                                                                                                |
| Engine (CE)                | The Colore Engine orchestrates request handling.                                                                                                                                                                                                                              |
| Request Adapter (RA)       | Once the request is dispatched by the entrypoint, the Adapter is initialized from the EE. The CE will then resolve the context through the RA. The RA is then consequently injected into Logic calls as a means to provide a consistent, unified abstraction layer to the EE. |
| Providers                  | Providers provide the RA and CE with EE agnostic functionality to look up contexts and provide other helpful functionality.                                                                                                                                                   |
| Logic                      | Aspect "Controllers" that provide separated layers of functionality, akin to middleware.                                                                                                                                                                                      |
| Renderers                  | They transform the render properties into something that can then be outputted back to the client through the RA.                                                                                                                                                             |

#### Adapters

Adapters provide an adaptive interface to the (outside) Execution Environment.

#### Providers

Providers provide informational services, like context resolution.

### Notes

At the time Colore was originally conceived, Node hadn't been created yet and Symphony had not come out with the Runtime environment.
