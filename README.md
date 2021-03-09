# UWork API

<p>
 This is the API for working with uwork service. 
</p>

### Table of contents

1.  [Notation](#used-notation-in-documentation)
2.  [Phone Format](#phone-format)
3.  [Information](#information)
4.  [Categories](#categories)
5.  [Locations](#locations)
6.  [Registration](#registration)
7.  [Authentication](#authentication-and-authorization)
8.  [User controller](#user-controller)
9.  [Profiles](#profiles)
10. [Specialities](#specialities)
11. [Reviews and views](#reviews-and-views)
12. [Search](#search)
13. [Favourite services](#favourites)
14. [Cards](#cards)
15. [Messages](#messages)

<p>
  For using API endpoints, all requests should have "API-TOKEN" header
  set to currently using token. Without it, all requests will return
  401 error.
</p>

<a id="used-notation-in-documentation" name="used-notation-in-documentation"></a>

## Used notation

<p>
    There are several types,
    that are used in specifications and they are listed below:
</p>
<table>
<thead>
<tr>
<th>Name</th>
<th>Specification</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
    <td>TString</td>
    <td>{lang: String}, lang = 'en'|'ru'</td>
    <td>
        Translatable string,
        contains several strings in various languages
    </td>
</tr>
<tr>
    <td>Category</td>
    <td>{
            name: TString,
            slug: TString,
            id: Int,
            icon_default: String|null,
            icon_active: String|null,
            parent_id: Int|null,
            children: Category[],
            parent: Category|null,
            is_baseline: bool,
            is_shown: bool
        }
    </td>
  <td>
    Entity representing some category. If is_baseline is true,
    categories' children won't be returned, but on search this category
    can be returned as parent.
  </td>
</tr>
<tr>
    <td>FAQ</td>
    <td>{
            id: Int,
            question: TString,
            answer: TString,
            order: Int
        }
    </td>
    <td>Element of FAQ section</td>
</tr>
<tr>
    <td>Region</td>
    <td>{
            name: TString,
            id: Int,
            cities: City[]
        }
    </td>
    <td>Entity representing certain region</td>
</tr>
<tr>
    <td>City</td>
    <td>{
            id: Int,
            name: TString,
            region_id: Int,
            region: Region,
            districts: District[]
        }
    </td>
    <td>Entity representing certain city</td>
</tr>
<tr>
    <td>District</td>
    <td>{
            id: Int,
            name: TString,
            city_id: Int,
            city: City
        }
    </td>
    <td>Entity representing certain district</td>
</tr>
<tr>
    <td>User</td>
    <td>{
            id: Int,
            first_name: String,
            last_name: String,
            father_name: String,
            notification_settings: Dictionary&lt;String, Boolen&gt;
        }
    </td>
    <td>User model</td>
</tr>
<tr>
    <td>Image&lt;T1, T2&gt;</td>
    <td>{
            id: Int,
            path: String,
            model: T1,
            model_type: String,
            model_id: Int,
            model_additional: T2|null
            model_additional_type: string
            model_additional_id: Int
        }
    </td>
    <td>Image model</td>
</tr>
<tr>
    <td>Profile</td>
    <td>{
      about: String,
      phone: String,
      picture: String|null,
      reviews_count: Number,
      rating_quality: Number,
      rating_price: Number,
      rating_time: Number,
      views_count: Number,
      open_count: Number,
      is_approved: Boolean,
      specialities: Speciality[],
      media: Image[]
    }
    </td>
    <td>Entity of speciality</td>
</tr>
<tr>
    <td>Speciality</td>
    <td>{
            category: Category,
            category_id: Int,
            price: Float,
            name: String,
            media: Image[],
        }
    </td>
    <td>Entity of speciality</td>
</tr>
<tr>
    <td>SpecialityForm</td>
    <td>{
            category_id: Int,
            price: Float,
            name: String,
        }
    </td>
    <td>Form for specifying specialities</td>
</tr>
<tr>
    <td>Review</td>
    <td>{
            id: Int,
            headline: String,
            text: String,
            rating_quality: Int,
            rating_price: Int,
            rating_time: Int,
        }
    </td>
    <td>Model that represents review</td>
</tr>
<tr>
    <td>CreateReviewForm</td>
    <td>{
            headline: String,
            text: String,
            rating_quality: Int,
            rating_price: Int,
            rating_time: Int,
        }
    </td>
    <td>Form for creating review</td>
</tr>
<tr>
    <td>CreateViewForm</td>
    <td>{
            opened: Boolean|null,
        }
    </td>
    <td>Form to register view</td>
</tr>
<tr>
    <td>View</td>
    <td>{
            user_id: Int,
            ip_addr: Float,
            opened: Boolean,
        }
    </td>
    <td>View element</td>
</tr>
<tr>
    <td>Card</td>
    <td>{
            id: int,
            number: String,
            label: String|null,
            expiration_month: Int,
            expiration_year: Int,
            cvv: Int
        }
    </td>
    <td>Model represents the view of card</td>
</tr>
<tr>
    <td>ObfuscatedCard</td>
    <td>{
            id: int,
            label: String|null,
            number_obfuscated: String,
            expiration_month: Int,
            expiration_year: Int
        }
    </td>
    <td>Model of card, but with hidden number</td>
</tr>
<tr>
    <td>Chat</td>
    <td>{
            id: int,
            initiator: User,
            acceptor: User,
            initiator_id: Int,
            acceptor_id: Int,
            last_message_time: Datetime|null
            unread_messages_count: Int|null
        }
    </td>
    <td>Grouping entity for messages</td>
</tr>
<tr>
    <td>Message</td>
    <td>{
            id: int,
            text: String|null,
            attachment: String|null,
            user: User,
            user_id: Int,
            chat: Chat,
            chat_id: Int,
            read_at: Datetime|null,
        }
    </td>
    <td>Model represents the view of message</td>
</tr>
<tr>
    <td>Pagination&lt;T&gt;</td>
    <td>{
            data: T[],
            current_page: Int,
            last_page: Int,
            total: Int,
            per_page: Int,
            next_page_url: String|null,
        }
    </td>
    <td>General model for paginating output</td>
</tr>
<tr>
    <td>Dictionary&lt;T1, T2&gt;</td>
    <td>{
        [T1]: T2, ...
        }
    </td>
    <td>Dictionary data structure</td>
</tr>
</tbody>
</table>

<a id="phone-format" name="phone-format"></a>

## Phone format

<p>
    Phone number should be in the following format:
    <span>{countryCode}{operatorCode}{number}</span>.
    No spaces, leading + or trailing zeros are allowed.
    "89050023456" is considered valid, but "+79050023456",
    "8 905 002 34 56", "8 (905)002-34-56" are not.
</p>

<a id="information" name="information"></a>

## Information

<p>
  Information about phone number, links to mobile applications,
  items in FAQ section and text in "About us"
</p>
<table>
<thead>
<th>Route</th>
<th>Method</th>
<th>Description</th>
<th>Response</th>
</thead>
<tbody>
<tr>
    <td>
        /api/info
    </td>
    <td>
        GET
    </td>
    <td>
        Returns general information
    </td>
    <td>
        {
          phone: String|null,
          apps: {android: String|null, ios: String|null}
        }
    </td>
</tr>
<tr>
    <td>
        /api/info/about
    </td>
    <td>
        GET
    </td>
    <td>
      Returns multilingual text for about us section 
    </td>
    <td>
        {
            about_us: TString
        }
    </td>
</tr>
<tr>
    <td>
        /api/info/faq
    </td>
    <td>
        GET
    </td>
    <td>
      Returns items in FAQ section
    </td>
    <td>
        {
            faq: FAQ[]
        }
    </td>
</tr>
</tbody>
</table>

<a id="categories" name="categories"></a>

## Categories

<p>
    There are 2 routes for fetching categories
</p>
<table>
<thead>
<th>Route</th>
<th>Method</th>
<th>Description</th>
<th>Response</th>
</thead>
<tbody>
<tr>
    <td>
        /api/categories
    </td>
    <td>
        GET
    </td>
    <td>
        Returns all categories including their children
    </td>
    <td>
        {
        categories: Category[]
        }
    </td>
</tr>
<tr>
    <td>
        /api/categories/{slug}
    </td>
    <td>
        GET
    </td>
    <td>
        Returns category information if exists.
        If there is error, returns error with status 404
    </td>
    <td>
        {
            categories: Category|null,
            error: String|null
        }
    </td>
</tr>
<tr>
    <td>
        /api/categories/search
    </td>
    <td>
        GET, {keyword: string}
    </td>
    <td>
      Performs search by category, including hidden ones
    </td>
    <td>
        {
            categories: Category|null,
        }
    </td>
</tr>
</tbody>
</table>

<a id="locations" name="locations"></a>

## Locations

<div>
    Information about location is divided into 3 groups:
    <ul>
        <li>Regions</li>
        <li>Cities</li>
        <li>Districts</li>
    </ul>
    They are hierarchily connected,
    so each region has multiple cities, each city multiple districts.
    <hr />
    Available endpoints listed below
    <table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Description</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/regions
        </td>
        <td>
            GET
        </td>
        <td>
            Returns all available regions
        </td>
        <td>
            {
                regions: Region[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/regions/{id}
        </td>
        <td>
            GET
        </td>
        <td>
          Returns information about specific region, including cities.
        </td>
        <td>
            {
                region: Region
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/regions/{id}/cities
        </td>
        <td>
            GET
        </td>
        <td>
            Returns information cities of specific region
        </td>
        <td>
            {
                cities: City[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/cities/{id}
        </td>
        <td>
            GET
        </td>
        <td>
            Returns information about specific city with districts.
        </td>
        <td>
            {
                city: City
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/cities/{id}/districts
        </td>
        <td>
            GET
        </td>
        <td>
            Returns information districts of specific city
        </td>
        <td>
            {
                districts: District[]
            }
        </td>
    </tr>
    </tbody>
    </table>
</div>

<a id="registration" name="registration"></a>

## Registration
<div>
<p>
Registration is performed in 3 steps:
</p>
<ul>
<li>
  User inputs phone number. Request is sent to /api/phones. 
  In response, client gets <i>verification_uuid</i> and verification code is sent to
  phone number.
</li>
<li>
  Client sends verification code to verification route (/api/verify/{uuid}) and gets
  status. If status is okay, then phone number is verified.
</li>
<li>
  To finish registration, client sends all other information,
  including verification uuid, to /api/register. If phone is verified and other 
  constraints are met, user will be created and you will be able to login.
</li>
</ul>
<p>
<i>
    Note: For development purposes, code is verification is disabled.
    For verifying phone, send any 6 letter code string to verification route.
</i>
</p>
<table>
<thead>
<tr>
<th>Route</th>
<th>Method</th>
<th>Description</th>
<th>Request</th>
<th>Response</th>
</tr>
</thead>
<tbody>
<tr>
<td>/api/phones</td>
<td>POST</td>
<td>Ask for verification</td>
<td>{phone: String}</td>
<td>{status: String|null,
    errors: String[]|null, error: String, verification_uuid: String|null}
</td>
</tr>
<tr>
<td>/api/verify/{uuid}</td>
<td>POST</td>
<td>Finishes registration</td>
<td>{code: String}</td>
<td>{status: String|null, errors: String[]|null, error: String}</td>
</tr>
<tr>
<td>/api/register</td>
<td>POST</td>
<td>Finishes registration</td>
<td>{
  first_name: String,
  last_name: String,
  father_name: String,
  email: String|null,
  verification_uuid: String,
  password: String,
  password_confirmation: String
}</td>
<td>{user: User|null, errors: String[]|null, error: String}</td>
</tr>
</tbody>
</table>
</div>

<a id="authentication-and-authorization" name="authentication-and-authorization"></a>

## Authentication and authorization

<div>
<p>
    For authorization, back-end uses JWT tokens which are being sent
    in "Authorization" header the following way: "Bearer {token}".
</p>
<p>
    In authentication, there can be used either email or phone,
    and password. Before login, user should verify phone number.
    After registration, 6 letter code is being sent to inputted number.
    For verifying code, you should know also UUID of verification.
    UUID is valid for 10 minutes. After that, you should resend code
    and get new UUID for verification.
</p>
<p>
    For resetting password, you first send post request to /passwords,
    then send request to /passwords/{uuid} to set new password. 
    UUID is valid for 4 hours and removed after first set.
</p>
<p>
<i>
    Note: For development purposes, code is verification is disabled.
    For verifying phone, send any 6 letter code string to verification route.
</i>
</p>
<hr />
<p>
    Endpoints are listed below:
</p>
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/verify/{uuid}
        </td>
        <td>
            POST
        </td>
        <td>
            {
                code: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                tries_left: Number|null,
                verification_uuid: String|null,
                status: String|null,
                user: User|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/login
        </td>
        <td>
            POST
        </td>
        <td>
            {
                email: String|null,
                phone: String|null,
                password: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                user: User|null,
                access_token: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/passwords
        </td>
        <td>
            POST
        </td>
        <td>
            {
                email: String|null,
                phone: String|null
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                uuid: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/passwords/{uuid}
        </td>
        <td>
            POST
        </td>
        <td>
            {
                password: String
                password_confirmation: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                user: User|null,
            }
        </td>
    </tr>
    </tbody>
    </table>
</div>

<a id="user-controller" name="user-controller"></a>

## User controller

<div>
<p>
    All routes are protected by authentication middleware,
    so in each request you should add authorization header.
</p>
<p>
    User can change basic information (names) by using sending PUT
    request. For changing email, phone and password, you should
    provide current password. If user tries to change phone,
    phone should be verified before changing. After verification,
    changes are applied.
</p>
<p>
  Settings can be changed with /api/user/settings route. You should
  provide settings as associative array, where keys are names of 
  settings and values are boolean values. (e.g. ['key1' => true])
</p>
<hr />
<p>
  List of available settings:
</p>
<table>
<thead>
<tr>
<th>Key</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>service_change_email</td>
<td>E-mail уведомления об изменениях статусов заказов</td>
</tr>
<tr>
<td>new_service_email</td>
<td>E-mail уведомления о новых заказов</td>
</tr>
<tr>
<td>important_events_sms</td>
<td>SMS уведомления о важных событих</td>
</tr>
</tbody>
</table>
<hr />
<p>
    List of available routes
</p>
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/user
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                user: User|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                first_name: String,
                last_name: String,
                father_name: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                user: User|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/emails
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                email: String,
                password: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                user: User|null,
                status: String|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/phones
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                phone: String,
                password: String,
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                user: User|null,
                verification_uuid: String|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/passwords
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                current_password: String,
                password: String,
                password_confirmation: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                user: User|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/settings
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                settings: Dictionary&lt;String, Bool&gt;
            }
        </td>
        <td>
            {
                errors: String[]|null,
                user: User|null,
            }
        </td>
    </tr>
    </tbody>
    </table>
</div>

<a id="profiles" name="profiles"></a>

## Profiles

<div>
<p>
    Set of routes for managing user's profiles.
    All routes should be accessed with authorization token.
    The user can have only one profile. If you try to create another,
    server will return 403 error.
</p>
<p>
    For creating profiles, /api/user/profiles are used.
    Once profile is created, it can't be deleted manually.
    Each user can have one profile with multiple specialities.
    Specialities contain information about price of work and
    category it's referred to. 
</p>
<p>
  Images can be assigned to the certain specialities. To do that,
  send request to /api/profile/images/{imageId} with specifying
  speciality id. If you want image to not refer to any speciality,
  send request with speciality_id=null
</p>
<hr>
<p>
    Set of available routes are listed below 
</p>

<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/user/profile
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                profile: Profile|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/profiles/{id}
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                profile: Profile|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile
        </td>
        <td>
            POST
        </td>
        <td>
            {
                about: String,
                phone: String|null,
                specialities: SpecialityForm[],
                images: Int[]|null,
                avatar: File|null
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                profile: User|null,
                verification_uuid: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/update
        </td>
        <td>
            POST
        </td>
        <td>
            {
                about: String|null,
                phone: String|null,
                avatar: File|null,
                images: Int[]|null
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                profile: User|null,
                verification_uuid: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/images/{imageId}
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                speciality_id: Int|null,
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                image: Image|null,
            }
        </td>
    </tr>
    </tbody>
    </table>
</div>

<a id="specialities" name="specialities"></a>

## Specialities

<p>
Routes used to manage specialities of the current user.
Information about other user's specialities is returned from profile
API. This section used to work only with the current user.
</p>
<p>
Before using these routes, ensure user has created profile.
Otherwise all routes will return 403 error. Once created,
speciality can change name and price, but not category.
</p>

<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/user/profile/specialities
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                specialities: Specialities[]|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities
        </td>
        <td>
            POST
        </td>
        <td>
            {
              category_id: Number,
              price: Number,
              name: String,
            }
        </td>
        <td>
            {
              errors: String[]|null,
              error: String|null,
              status: String|null,
              speciality: Speciality|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/{specialityId}
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                price: Number|null,
                name: String|null,
            }
        </td>
        <td>
            {
              errors: String[]|null,
              error: String|null,
              status: String|null,
              speciality: Speciality|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/{specialityId}
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                status: String|null,
                deleted: Bool|null,
            }
        </td>
    </tr>
    </tbody>
    </table>


<a id="reviews-and-views" name="reviews-and-views"></a>

## Reviews and views

<p>
    Each review has headline, text and rating.
    One user can have a review per profile, 
    not including its own profile. Same is applying
    for views.
</p>
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/user/profile/reviews
        </td>
        <td>
            GET
        </td>
        <td></td>
        <td>
            {
                reviews: Pagination&lt;Review&gt;|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/{profileId}/reviews
        </td>
        <td>
            GET
        </td>
        <td></td>
        <td>
            {
                reviews: Pagination&lt;Review&gt;|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profiles/{profile}/reviews
        </td>
        <td>
            POST
        </td>
        <td>
            CreateReviewForm
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                review: Review|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profiles/{profile}/reviews
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/profiles/{profile}/views
        </td>
        <td>
            DELETE
        </td>
        <td>CreateViewForm</td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                view: View|null,
            }
        </td>
    </tr>
    </tbody>
</table>

<a name="search" id="search"></a>

## Search

<p>
    These routes provide way to search through profiles on site.
    results are returned in paginated format. For loading next page data,
    exact query should be provided (including number of page).
    There are 3 criteria to search the profile:
</p>
<ul>
    <li>
        Keyword - first category is searched by keyword, then
    search are limited by the category. If no category found, returns nothing
    </li>
    <li>
        Category - filters profile by having the speciality within given
        category
    </li>
    <li>
        Location - consists of region_id, city_id and district_id. 
        By leaving one empty profile are not being filtered by the field.
    </li>
</ul>
<p>
    Autocomplete works the following way: you send request to autocomplete
    route, providing keyword as query parameter
    and get list of suggestions.
</p>
<hr />
<p>
    List of routes are listed below:
</p>
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/profiles
        </td>
        <td>
            GET
        </td>
        <td>
            {
                keyword: string|null,
                category_id: int|null,
                region_id: int|null,
                city_id: int|null,
                district_id: int|null,
                per_page: int|null,
                page: int|null
            }
        </td>
        <td>
            {
                result: Pagination&lt;Profile&gt;
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/autocomplete
        </td>
        <td>
            GET
        </td>
        <td>
            {
                keyword: string
            }
        </td>
        <td>
            {
                suggestions: string[]
            }
        </td>
    </tr>
    </tbody>
</table>

<a id="favourites" name="favourites"></a>

## Favourites

<p>
Routes to add services (profile's speciality) as favourite.
User can't add his own services as favourite. Non authorized users
can't have favourites.
</p>
<hr />
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/user/favourites
        </td>
        <td>
            GET
        </td>
        <td>
            {
                page: int|null
            }
        </td>
        <td>
            {
                services: Services&lt;Profile&gt;
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/favourites/{serviceId}
        </td>
        <td>
            POST
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                status: String|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/favourites/{serviceId}
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                status: String
            }
        </td>
    </tr>
    </tbody>
</table>

<a id="cards" name="cards"></a>

## Saved cards

<p>
Saved cards are available only to authorized users.
For creating card, user should provide:
number (as string), expiration month, expiration year, cvv, name on card
and can provide label for the card. When updating, only expiration date
and label can be changed. When getting cards, only label, obfuscated
name and expiration date is shown
</p>
<hr />
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/user/cards
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                cards: ObfuscatedCard[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/cards
        </td>
        <td>
            POST
        </td>
        <td>
            Card
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                card: ObfuscatedCard|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/cards/{cardId}
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                label: String|null,
                expiration_month: Integer|null,
                expiration_year: Integer|null
            }
        </td>
        <td>
            {
                errors: String|null,
                error: String|null,
                status: String|null,
                card: ObfuscatedCard,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/cards/{cardId}
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                status: String|null,
                card_found: Boolean,
            }
        </td>
    </tr>
    </tbody>
</table>

<a id="messages" name="messages"></a>

## Messages

<p>
Routes for using messenger system. Only authenticated users can use these
routes. User can't send message to itself. Chats are created automatically,
but can be deleted manually.
</p>
<p>
To mark all messages as read, send PUT request to /api/chats/{userId}
</p>
<hr />
<table>
    <thead>
        <th>Route</th>
        <th>Method</th>
        <th>Request</th>
        <th>Response</th>
    </thead>
    <tbody>
    <tr>
        <td>
            /api/chats
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                chats: Chat[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/chats/{userId}
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                messages: Paginate&lt;Message&gt;String[]|null,
                error: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/chats/{userId}/search
        </td>
        <td>
            GET
        </td>
        <td>
          {
            keyword: String
          }
        </td>
        <td>
            {
                messages: Paginate&lt;Message&gt;String[]|null,
                error: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/chats/{userId}
        </td>
        <td>
            POST
        </td>
        <td>
            {
                text: String|null
                attachment: File|null
            }
        </td>
        <td>
            {
                errors: String|null,
                error: String|null,
                status: String|null,
                message: Message|null,
                chat: Chat|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/chats/{userId}
        </td>
        <td>
            PUT
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                status: String|null,
                count: Int|null,
                chat: Chat|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/chats/{userId}
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                status: String|null,
                deleted: Boolean,
            }
        </td>
    </tr>
    </tbody>
</table>