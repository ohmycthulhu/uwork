# UWork API

<p>
 This is the API for working with uWork service. 
</p>

### Table of contents

1. [Notation](#notation)
2. [Phone Format](#phone-format)
3. [Information](#information)
4. [Categories](#categories)
5. [Locations](#locations)
6. [Registration](#registration)
7. [Authentication](#authentication-and-authorization)
8. [User controller](#user-controller)
9. [Profiles](#profiles)
10. [Specialities](#specialities)
11. [Reviews and views](#reviews-and-views)
12. [Search](#search)
13. [Favourite services](#favourites)
14. [Cards](#cards)
15. [Messages](#messages)
16. [Communication](#communication)
17. [Notifications](#notifications)
18. [Complaints](#complaints)

<p>
  For using API endpoints, all requests should have "API-TOKEN" header
  set to currently using token. Without it, all requests will return
  401 error.
</p>

<a id="notation" name="notation"></a>

## Notation

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
    <td>Category</td>
    <td>{
            name: String,
            slug: String,
            id: Int,
            icons: {default: String|null, selected: String|null},
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
            question: String,
            answer: String,
            order: Int
        }
    </td>
    <td>Element of FAQ section</td>
</tr>
<tr>
    <td>Region</td>
    <td>{
            name: String,
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
            name: String,
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
            name: String,
            city_id: Int,
            city: City
        }
    </td>
    <td>Entity representing certain district</td>
</tr>
<tr>
    <td>Subway</td>
    <td>{
            id: Int,
            name: String,
            color: String|null,
            identifier: String|null,
            line: String|null,
            city_id: Int,
            district_id: Int|null,
        }
    </td>
    <td>Entity representing certain subway</td>
</tr>
<tr>
    <td>User</td>
    <td>{
            id: Int,
            first_name: String,
            last_name: String,
            father_name: String|null,
            about: String|null,
            birthdate: String|null,
            is_male: Boolean|null,
            notification_settings: Dictionary&lt;String, Boolen&gt;,
            avatar_url: String|null,
            avatar_image: Image|null,
            region_id: Int|null,
            city_id: Int|null,
            district_id: Int|null
            subway_id: Int|null
        }
    </td>
    <td>User model</td>
</tr>
<tr>
    <td>Image&lt;T1, T2&gt;</td>
    <td>{
            id: Int,
            path: String,
            responsive_image_urls: Dictionary&lString,String&gt,
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
      positive_rating_ratio: Number,
      reviews_count: Number,
      rating: Number,
      rating_quality: Number,
      rating_price: Number,
      rating_time: Number,
      views_count: Number,
      open_count: Number,
      is_approved: Boolean,
      specialities: Speciality[]
      speciality: Speciality|Null
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
            description: String,
            media: Image[],
        }
    </td>
    <td>Entity of speciality</td>
</tr>
<tr>
    <td>CategoryOccurrence</td>
    <td>{
            category: Category,
            count: Int
        }
    </td>
    <td>Entity of speciality</td>
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
    <td>ReviewCount</td>
    <td>{
            speciliaty_id: Int|null,
            total: Int,
        }
    </td>
    <td>Statistics entity for reviews count</td>
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
            read_at: Datetime|null
        }
    </td>
    <td>Model represents the view of message</td>
</tr>
<tr>
    <td>Notification</td>
    <td>{
            id: int,
            user_id: Number,
            notifiable_type: Number,
            notifiable_id: Number,
            read_at: Datetime|null,
            title: String,
            description: String|null
        }
    </td>
    <td>Model represents notification element</td>
</tr>
<tr>
    <td>Complaint</td>
    <td>{
            id: int,
            user_id: Number|null,
            type_id: Number|null,
            reason_other: String|null,
            text: String
        }
    </td>
    <td>Model represents a single complaint</td>
</tr>
<tr>
    <td>ComplaintType</td>
    <td>{
            id: int,
            name: String
        }
    </td>
    <td>Model represents complaint type</td>
</tr>
<tr>
    <td>AppealReason</td>
    <td>{
            id: int,
            name: String,
        }
    </td>
    <td>Reason for appealing</td>
</tr>
<tr>
    <td>Range</td>
    <td>{
            min: float,
            max: float,
        }
    </td>
    <td>Reason for appealing</td>
</tr>
<tr>
    <td>Appeal</td>
    <td>{
            id: int,
            text: String|null,
            appeal_reason: AppealReason|null,
            appeal_reason_other: String|null,
            phone: String|null,
            email: String|null,
            name: String|null
        }
    </td>
    <td>Model represents the view of message</td>
</tr>
<tr>
    <td>HelpCategory</td>
    <td>{
            id: int,
            name: String,
            slug: String,
            order: Number|null,
            top_items: HelpItem[]|null,
            items: HelpItem[]|null,
            items_count: Number|null
        }
    </td>
    <td>Model for help category</td>
</tr>
<tr>
    <td>HelpItem</td>
    <td>{
            id: int,
            name: String,
            slug: String,
            order: Number|null,
            text: String,
            help_category_id: Number
        }
    </td>
    <td>Model for help category</td>
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
<p>
  Certain pages should have "Is useful" button. To collect statistics,
  there are "/api/info/text-statistics" routes, that take type as route
  parameter and by method type either upvote or downvote the text.
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
          public_offer: String|null,
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
            about_us: String
        }
    </td>
</tr>
<tr>
    <td>
        /api/info/{type}
    </td>
    <td>
        GET
    </td>
    <td>
      Returns text information as {name: String|null, text: String|null}
    </td>
    <td>
        {
            status: string,
            text: integer
        }
    </td>
</tr>
<tr>
    <td>
        /api/info/{type}/statistics
    </td>
    <td>
        POST, PUT
    </td>
    <td>
      Adds upvote to the text
    </td>
    <td>
        {
            status: string,
            amount: integer
        }
    </td>
</tr>
<tr>
    <td>
        /api/info/{type}/statistics
    </td>
    <td>
        DELETE
    </td>
    <td>
      Adds downvote to the text
    </td>
    <td>
        {
            status: string,
            amount: integer
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
<tr>
    <td>
        /api/help-categories
    </td>
    <td>
        GET
    </td>
    <td>
      Returns list of help categories with top items
    </td>
    <td>
        {
            status: String,
            categories: HelpCategory[]
        }
    </td>
</tr>
<tr>
    <td>
        /api/help-categories/{slug}
    </td>
    <td>
        GET
    </td>
    <td>
      Returns help category with items
    </td>
    <td>
        {
            status: String,
            error: String|null,
            category: HelpCategory|null
        }
    </td>
</tr>
<tr>
    <td>
        /api/help-items/{slug}
    </td>
    <td>
        GET
    </td>
    <td>
      Returns help item
    </td>
    <td>
        {
            status: String,
            error: String|null,
            item: HelpItem|null
        }
    </td>
</tr>
</tbody>
</table>

<a id="categories" name="categories"></a>

## Categories

<p>
    There are 3 routes for fetching categories
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
        /api/categories/{id}
    </td>
    <td>
        GET
    </td>
    <td>
        Returns category information if exists.
        If there is error, returns error with status 404.
        The default nesting level is 2, but can be controlled by passing level argument.
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
        GET, {keyword: string, parent_id: Integer|null}
    </td>
    <td>
      Performs search by category, including hidden ones.
      If parent id is provided, search is limited by only child
      categories.
    </td>
    <td>
        {
            categories: Category[]|null
        }
    </td>
</tr>
</tbody>
</table>

<a id="locations" name="locations"></a>

## Locations

<div>
    Information about location is divided into 4 groups:
    <ul>
        <li>Regions</li>
        <li>Cities</li>
        <li>Districts</li>
        <li>Subways</li>
    </ul>
    They are hierarchy connected,
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
            Returns all available regions, with attached cities.
            Use detailed = 0 to get regions without cities. 
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
          Returns information about specific region, including cities, but without districts.
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
            Returns information cities with districts of specific region
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
    <tr>
        <td>
            /api/cities/{id}/subways
        </td>
        <td>
            GET
        </td>
        <td>
            Returns information subways of specific city
        </td>
        <td>
            {
                subways: District[]
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
<p>
  If user with the exact phone number is deleted from the system,
  then on verifying API will restore him and return token for authorization.
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
<td>{status: String|null, user: User|null, errors: String[]|null, error: String, token: String|null}</td>
</tr>
<tr>
<td>/api/register</td>
<td>POST</td>
<td>Finishes registration</td>
<td>{
  first_name: String,
  last_name: String,
  father_name: String|null,
  about: String|null,
  email: String|null,
  verification_uuid: String,
  is_male: Boolean|null,
  birthdate: Date|null,
  avatar: File|null,
  region_id: Number|null,
  city_id: Number|null,
  district_id: Number|null,
  subway_id: Number|null,
  password: String
}</td>
<td>{user: User|null, token: String|null, errors: String[]|null, error: String}</td>
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
    and password. On registration, 6 letter code is being sent to inputted number.
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
    For login or registration via messenger, you should send request to
    /api/tokens/{uuid} . If user exists, then user and token is returned.
    Otherwise, verification_uuid is returned for registration.
</p>
<p>
  To reset the password do the following:
</p>
<ol>
<li>Send request to /api/passwords with using the phone or email address. Use will receive the code and you will get uuid</li>
<li>To verify the uuid, send 6 symbol code to /api/passwords/verify using the uuid and code. If result is 200, everything is okay</li>
<li>Send request with new password to /api/passwords/{uuid}. If uuid is verified, new password will be set</li>
</ol>
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
            /api/refresh
        </td>
        <td>
            POST
        </td>
        <td>
            {}
        </td>
        <td>
            {
                error: String|null,
                user: User|null,
                access_token: String|null,
                ttl: Number|null
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/logout
        </td>
        <td>
            POST
        </td>
        <td>
            {}
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
            /api/tokens/{uuid}
        </td>
        <td>
            POST
        </td>
        <td>
            {
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                user: User|null,
                phone: String|null,
                access_token: String|null,
                verification_uuid: String|null
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
            /api/passwords/verify
        </td>
        <td>
            POST
        </td>
        <td>
            {
                uuid: String,
                code: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null
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
    request. For changing the password you should
    provide current password.
</p>
<p>
  To delete profile, use /api/user with DELETE request.
</p>
<p>
  Settings can be changed with /api/user/settings route. You should
  provide settings as associative array, where keys are names of 
  settings and values are boolean values. (e.g. ['key1' => true])
</p>
<p>
  Changing the phone number goes in the following steps:
</p>
<ol>
<li>
User sends request to /user/phones with new phone. Response contains verification uuid for verifying the number.
The code is being sent to user's phone number
</li>
<li>
Verification code with verification uuid is being sent to verification route (/api/verify/{uuid}).
If the code is correct, phone will be validated and phone in user profile will be changed.
</li>
</ol>
<hr />
<p>
To delete avatar, send DELETE request to /api/user/avatar.
</p>
<hr />
<p>
  To change user phone, follow the steps:
</p>
<oi>
<li>Send request to /api/phones with existing phone number and parameter 'existing' as 1</li>
<li>Verify the phone number using phone verification API</li>
<li>Send request to /api/user/phones with new phone number and verification uuid from previous requests</li>
</oi>
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
            DELETE
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
            /api/user
        </td>
        <td>
            PUT, POST
        </td>
        <td>
            {
                first_name: String,
                last_name: String,
                father_name: String|null,
                about: String|null,
                avatar: File|null,
                email: String|null,
                birthdate: String|null,
                is_male: Boolean|null,
                region_id: Int|null,
                city_id: Int|null,
                district_id: Int|null
                subway_id: Int|null
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
            /api/user/avatar
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                user: User|null
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
                verification_uuid: String
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
<hr>
<p>
Used request forms:
</p>
<table>
<thead>
<tr>
<th>
Name
</th>
<th>
Specification
</th>
</tr>
</thead>
<tbody>
<tr>
<td>SpecialityForm</td>
<td>
{ category_id: Int, price: Float|null, name: String|null, images: Array&lt;ID&gt;|null }	
</td>
</tr>
</tbody>
</table>
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
                about: String|null,
                phone: String|null,
                specialities: SpecialityForm[]
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
<p>
  For managing specialities' images, use .../images routes.
  For reordering, you can update <i>order_column</i> field
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
          {
            category_id: Int|null
          }
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
            /api/user/profile/specialities/categories
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                result: CategoryOccurrence[]|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/categories/{parentId}
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                result: CategoryOccurrence[]|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/categories/search
        </td>
        <td>
            GET
        </td>
        <td>
          {
            keyword: String,
            parent_id: ID|null,
            size: Number|null
          }
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                result: CategoryOccurrence[]|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/categories/{categoryId}
        </td>
        <td>
            POST
        </td>
        <td>
          {
            price: Number|null,
            name: String|null,
            description: String|null,
            images: Array&lt;ID&gt;|null,
          }
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                result: CategoryOccurrence[]|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/categories/{categoryId}
        </td>
        <td>
            DELETE
        </td>
        <td>
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                result: CategoryOccurrence[]|null,
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
              images: Array&lt;ID&gt;|null
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
            /api/user/profile/specialities/{categoryId}
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
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
            PUT, POST
        </td>
        <td>
            {
                price: Number|null,
                name: String|null,
                description: String|null,
                images_remove: Array&lt;ID&gt;|null,
                images_add: Array&lt;ID&gt;|null
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
    <tr>
        <td>
            /api/user/profile/specialities/{specialityId}/images
        </td>
        <td>
            POST
        </td>
        <td>
            {
              image: File
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
    <tr>
        <td>
            /api/images
        </td>
        <td>
            POST
        </td>
        <td>
            {
              image: File,
              order: Number|null,
            }
        </td>
        <td>
            {
              errors: String[]|null,
              error: String|null,
              status: String|null,
              media: Image|null,
              url: String|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/profile/specialities/{specialityId}/images/{imageId}
        </td>
        <td>
            PUT
        </td>
        <td>
            {
                order_column: Number
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
    <tr>
        <td>
            /api/user/profile/specialities/{specialityId}/images/{imageId}
        </td>
        <td>
            DELETE
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
<p>
  To reply to review, send request to /profiles/{profile}/reviews/{reviewId}
</p>
<p>
Used request forms:
</p>
<table>
<thead>
<tr>
<th>
Name
</th>
<th>
Specification
</th>
</tr>
</thead>
<tbody>
<tr>
<td>CreateReviewForm</td>
<td>
{
  headline: String,
  text: String,
  rating_quality: Int,
  rating_price: Int,
  rating_time: Int
}
</td>
</tr>
<tr>
<td>CreateViewForm</td>
<td>
{
  opened: Boolean|null,
}
</td>
</tr>
</tbody>
</table>
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
            /api/profiles/{profileId}/reviews
        </td>
        <td>
            GET
        </td>
        <td>{speciality_id: Int|null}</td>
        <td>
            {
                reviews: Pagination&lt;Review&gt;|null,
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/profiles/{profileId}/reviews/count
        </td>
        <td>
            GET
        </td>
        <td></td>
        <td>
            {
                counts: ReviewCount[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/profiles/{profile}/reviews
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
            /api/profiles/{profile}/reviews/{reviewId}
        </td>
        <td>
            POST
        </td>
        <td>
            {headline: String, text: String}
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
            /api/profiles/{profile}/reviews
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
            POST
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
        Location - consists of region_id, city_id, district_id and subway_id. 
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
                category_id: int|null,
                categories: Array&lt;int&gt;
                region_id: int|null,
                city_id: int|null,
                district_id: int|null,
                districts: Array&lt;int&gt;|null,
                subway_id: int|null,
                subways: Array&lt;int&gt;|null,
                per_page: int|null,
                page: int|null,
                sort_by: "price"|"district"|"rating"|null,
                sort_dir: "asc"|"desc"|null,
                price_min: Number|null,
                price_max: Number|null,
                rating_min: Number|null,
                rating_max: Number|null,
                ratings: Array&lt;Range&gt;|null
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
            /api/profiles/random
        </td>
        <td>
            GET
        </td>
        <td>
            {
                category_id: int|null,
                amount: int|null,
            }
        </td>
        <td>
            {
                status: string|null,
                error: string|null,
                profiles: Profile[]|null,
                category: Category|null,
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

## Cards

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


<a id="communication" name="communication"></a>

## Communication

<div>
<p>
At the moment, the only communication supported is appeals.
To use appeals, user can either choose appeal reason or write something in "Other".
Authenticated users may not send name, email and/or phone number.
There is a limit of 3 appeals in 4 hours.
If limit exceeds, 405 error will be returned
</p>
<p>
Used request forms:
</p>
<table>
<thead>
<tr>
<th>
Name
</th>
<th>
Specification
</th>
</tr>
</thead>
<tbody>
<tr>
<td>AppealRequest</td>
<td>
{
  text: String,
  appeal_reason_id: Int|null,
  appeal_reason_other: String|null,
  name: String|null,
  phone: String|null,
  email: String|null
}
</td>
</tr>
</tbody>
</table>
<hr />
<p>
  List of routes are shown below:
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
            /api/appeal-reasons
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                reasons: AppealReason[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/appeals
        </td>
        <td>
            POST
        </td>
        <td>
          AppealRequest
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                appeal: Appeal|null
            }
        </td>
    </tr>
    </tbody>
</table>
</div>

<a id="notifications" name="notifications"></a>

## Notifications

<div>
<p>
Notifications are connected to user and at the moment
only to the profile. It can change later. There are 2
endpoints: to get notifications and to mark as read.
For getting, you can specify if you want to get only
unread and size of chunk.
When reading, you can either send array with IDs or
not specify (every unread notification will be marked)
</p>
<p>
Used request forms:
</p>
<table>
<thead>
<tr>
<th>
Name
</th>
<th>
Specification
</th>
</tr>
</thead>
<tbody>
<tr>
<td>RetrieveNotifications</td>
<td>
{
  unread_only: Boolean|null,
  amount: Number|null,
}
</td>
</tr>
<tr>
<td>ReadNotifications</td>
<td>
{
  ids: Array|null,
}
</td>
</tr>
</tbody>
</table>
<hr />
<p>
  List of routes are shown below:
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
            /api/user/notifications
        </td>
        <td>
            GET
        </td>
        <td>
          RetrieveNotifications
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                notifications: Pagination&lt;Notification&gt;
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/user/notifications
        </td>
        <td>
            GET,PUT
        </td>
        <td>
          ReadNotifications
        </td>
        <td>
            {
                errors: String[]|null,
                status: String|null,
                count: Number|null
            }
        </td>
    </tr>
    </tbody>
</table>
</div>

<a id="complaints" name="complaints"></a>

## Complaints

<div>
<p>
For creating complaint, send request specifying
review and profile. You can't get complaints, only
create them.
</p>
<hr />
<p>
Used request forms:
</p>
<table>
<thead>
<tr>
<th>
Name
</th>
<th>
Specification
</th>
</tr>
</thead>
<tbody>
<tr>
<td>CreateComplaint</td>
<td>
{
  type_id: Number|null,
  reason_other: String|null,
  text: String
}
</td>
</tr>
</tbody>
</table>
<hr />
<p>
  List of routes are shown below:
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
            /api/complaint-types
        </td>
        <td>
            GET
        </td>
        <td>
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                types: ComplaintType[]
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/profiles/{profileId}/complaints
        </td>
        <td>
            POST
        </td>
        <td>
          CreateComplaint
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                complaint: Complaint
            }
        </td>
    </tr>
    <tr>
        <td>
            /api/profiles/{profileId}/reviews/{reviewId}/complaints
        </td>
        <td>
            POST
        </td>
        <td>
          CreateComplaint
        </td>
        <td>
            {
                errors: String[]|null,
                error: String|null,
                status: String|null,
                complaint: Complaint
            }
        </td>
    </tr>
    </tbody>
</table>
</div>