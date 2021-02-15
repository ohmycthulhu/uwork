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
