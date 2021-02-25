# UWork API

<p>
 This is the API for working with uwork service. 
</p>

### Table of contents

1. [Notation](#used-notation-in-documentation)
2. [Categories](#categories)
3. [Locations](#locations)
4. [Authentication](#authentication-and-authorization)
5. [User controller](#users-controller)
6. [Profiles](#profiles)
7. [Reviews and views](#reviews-and-views)
8. [ Search. ](#se)

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
            parent_id: Int|null,
            children: Category[],
            parent: Category|null
        }
    </td>
    <td>Entity representing some category</td>
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
            father_name: String
        }
    </td>
    <td>User model</td>
</tr>
<tr>
    <td>SpecialityForm</td>
    <td>{
            category_id: Int,
            price: Float,
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
            rating: Int,
        }
    </td>
    <td>Model that represents review</td>
</tr>
<tr>
    <td>CreateReviewForm</td>
    <td>{
            headline: String,
            text: String,
            rating: Int,
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
    <td>Model represents the view of profile</td>
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
    User can resend code, but only 3 times in an hour.
    After that, API will return error while trying to reset code.
    For verifying code, you should know also UUID of verification.
    UUID is valid for 10 minutes. After that, you should resend code
    and get new UUID for verification.
</p>
<p>
    For resetting password, you first send post request to /reset,
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
            /api/register
        </td>
        <td>
            POST
        </td>
        <td>
            {
                first_name: String,
                last_name: String,
                father_name: String,
                email: String,
                phone: String,
                password: String,
                password_confirmation: String
            }
        </td>
        <td>
            {
                errors: String[]|null,
                user: User|null,
                verification_uuid: String|null
            }
        </td>
    </tr>
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
            /api/resend/{phone}
        </td>
        <td>
            POST
        </td>
        <td>
        </td>
        <td>
            {
                error: String|null,
                verification_uuid: String|null,
                status: String|null,
                uuid: String|null,
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

<a id="users-controller" name="users-controller"></a>

## Users controller

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
                images: Int[]|null,
                remove_specialities: Int[]|null
                add_specialities: SpecialityForm[]|null
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
                result: Pagination&lt;Profile&gt;,
            }
        </td>
    </tr>
    </tbody>
</table>