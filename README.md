# UWork API

<p>
 This is the API for working with uwork service. 
</p>

### Used notation in Documentation
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
</tbody>
</table>

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
    </tbody>
    </table>
</div>
