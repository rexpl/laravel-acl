# Main concept explained

![Exemple](/docs/img/exemple-readme.jpg)

The following guide aims to help understand the concept behind this package, to learn how to use this package please follow the [documentation](/docs/README.md).

### Permissions

Permission determine if a user is authorized to perform a given action. Permissions can be used in [gates](https://laravel.com/docs/9.x/authorization#gates) or in [policies](https://laravel.com/docs/9.x/authorization#creating-policies).

### Access

Access determine if a user can read, write or delete a specific ressource, access is always used in combination with permission. Access is typically used in [policies](https://laravel.com/docs/9.x/authorization#creating-policies).

> ### :warning: Important to remember
>
> - Child groups only inherit [permission](#permissions) from parent groups
> - Parents only inherit [access](#access) from child groups

## Exemple for record USR 1

### User 1 can read and write this record

![exemple-readme-user-1-usr-1](/docs/img/zoom/exemple-readme-user-1-usr-1-zoom.jpg)

- **Read and Write**
    - Permission: The user belongs to *Group 1 users* wich inherits **Permission 3** and **Permission 4** from the **Users** group, giving read and write access to the USR model.
    - Access: The user belongs to **Group 1 users** wich has read and write access to this record.
- **Delete**
    - Permission: The user belongs to **Group 1 users** wich does not have **Permission 5**, not giving delete access to the USR model. **Group 1 users** does not inherit **Permission 5** from his parent group(s).
    - Access: The user belongs to **Group 1 users** wich doesn't have delete access to this record and the group doesn't inherit it from a child group.

### User 2 cannot access this record

![exemple-readme-user-1-usr-1](/docs/img/zoom/exemple-readme-user-2-usr-1-zoom.jpg)

- **Read, Write and Delete**
    - Permission: The user belongs to **Group 4 users** wich inherits **Permission 3** and **Permission 4** from the **Users** group, giving read and write access to the USR model.
    - Access: The user belongs to **Group 4 users** wich doesn't have any access on the record therefore disallowing read, write and delete.


### User 3, 4 and 5 cannot access this record

![exemple-readme-user-1-usr-1](/docs/img/zoom/exemple-readme-user-3-4-5-usr-1-zoom.jpg)

- **Read, Write and Delete**
    - Permission: All those users belong to groups wich inherit read and write permissions from the **Users** group.
    - Access: All those users belong to groups wich doesn't have any access on the record therefore disallowing read, write and delete.

### User 6 can read, write and delete this record

![exemple-readme-user-1-usr-1](/docs/img/zoom/exemple-readme-user-6-usr-1-zoom.jpg)

- **Read and Write**
    - Permission: The user belongs to **Users** wich has **Permission 3** and **Permission 4**, giving read and write access to the USR model.
    - Access: The user belongs to **Users** group wich has read, write and delete access on this record.
- **Delete**
    - Permission: The user belongs to **Admin** wich has **Permission 5**, giving delete access to the USR model.
    - Access: The user belongs to **Users** group wich has delete access on this record.

> **Note:** The **Users** group is parent of the **Group 1 users** wich has read and write access. Meaning that the user would still have read and write access even if the **Users** group didn't have access to the record.

### User 7 cannot acces this record

![exemple-readme-user-1-usr-1](/docs/img/zoom/exemple-readme-user-7-usr-1-zoom.jpg)

- **Read, Write and Delete**
    - Permission: The user is not in any group wich has the right permissions.
    - Access: The user is not in any group wich has access to the record.

### Guests cannot access this record

![exemple-readme-user-1-usr-1](/docs/img/zoom/exemple-readme-user-guest-usr-1-zoom.jpg)

- **Read, Wirte and Delete**
    - Permission: The user is not in any group wich has the right permissions.
    - Access: The user is not in any group wich has access to the record.
