![Thumbnail](images/thumbnail.png)
# Trusted identity handling framework for digital transformation
![image](https://img.shields.io/badge/License-MIT-green)

This repository houses a source code of a proof-of-concept implementation of an EIDAS-compatible digital identity ecosystem. The present project is written in PHP and designed to run on Apache web servers as several separate nodes of a framework.

## SID 2023
The project in this repository is the topic of a submission for the [Sibiu Innovation Days 2023](https://events.ulbsibiu.ro/innovationdays/) Hackathon. It aims to come as the present-day digital landscape in the country is devoid of any consistent digitalisation project and communication to and interest of the public regarding the technical details of such a system is relatively poor.

## Components
The *nodes* of the *framework* mentioned above are, briefly, as follows:
- **Connection provider** - Internally called *Ro-Connect* by us, it acts as a middleware between all the pre-existing authentication schemes and services and all the online public services that rely on them. Besides handling *authentication* i.e., the process of assuring of a user's identity, it also handles *authorization* i.e., ensuring that the user actually consents to share their data with certain parties. This node is designed according to the Authorization Code Flow of the [OpenID Connect](https://openid.net/specs/openid-connect-core-1_0.html) protocol, which relies on OAuth2.0, handling the flow of JSON Web Tokens for keeping track of permissions and identity, rather than states. This node also relies on a MySQL database for whitelisting other nodes in the network.
- **Dummy service provider** - This is a brief outline of any party desiring to rely on the *connection provider* to obtain proof-checked user identity data. This node has been implemented to showcase the interaction with the nodes holding other roles in this framework and acts as an OpenID Connect client.
- **Identity provider** - This role allows for authentication by any methods desired, but there exist several standards for assurance levels, corresponding to one-factor authentication, soft two-factor (as is the case for 2FA codes), and hard two-factor. The last standard forsees the use of a physical key for authentication. In accordance with the last regulation and policy changes worldwide, but especially in the European space, electronic IDs have been issued in a variety of countries following a common [standard](https://www.id.ee/wp-content/uploads/2021/08/td-id1-chip-app-4.pdf): a secure smart card chip storing a secret key and a certificate, signed by the nationally trusted authority, certifying the identity of the holder. As such, the identity provider designed by us, named internally *Ro-ID*, consists of two parts, a PHP server-side program and API, handling login attempts and issuing a chellenge for every attempt via a QR-code, and an app (designed for Android at the moment), which "asks" the user's ID card (using the user's PIN) to digitally sign the challenge and send the signature to the API, which can authenticate the user.

![Framework](images/framework.png)
*The framework design*

## More technical details
All the communication takes place under TLS (HTTPS protocol), with different domains, each with its self-signed SLL certificate, to ensure data integrity and protection. For security reasons, private keys which are referenced throughout the code have been omitted from this repository.
As eID rollout has been slow in the country, showcasing the application is performed using an eID simulator app such as [PersoSim](https://persosim.secunet.com/en/) installed on an NFC-, Host-Based Card Emulation enabled smartphone.

## Discussion and proposed development
Such frameworks have been flight-tested in several European countries as of now, with notable mentions for the [e-Estonia](https://e-estonia.com/solutions/interoperability-services/x-road/) project or France's [FranceConnect](https://franceconnect.gouv.fr/). Such benefits service providers and users alike, as the reliability of the identity data can be established, while ensuring convenience from the user's point of view as well. The use of a token-based design allows for less need for centralised storage.

As this framework is designed with scalability in mind, it forsees the development of a different role, that of **data providers**, that handle user data and make them available to service providers, that is through an authorization API the user is in control of.
Building on this concept, another pillar of such a framework would be the use and standardisation of digitally signed documents, with a interfaces for signing, as well as for instantly uploading and verifying a signed document, thus streamlining any official action online.
