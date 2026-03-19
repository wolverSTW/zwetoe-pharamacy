# Automated Testing Walkthrough

Here is an explanation of the automated testing setup for your project. 

## Backend Testing (Laravel)

The backend relies on PHPUnit, the default testing tool that comes with Laravel. The Feature tests are stored inside `backend/tests/Feature`.

The following tests were created to cover the core features:
- `AuthTest.php`: Ensures the `/api/login` route blocks invalid credentials.
- `AdminTest.php`: Protects the `/api/v1/admin/stats` route from non-admin and unauthenticated users.
- `MedicineTest.php` and `CategoryTest.php`: Ensures product and category endpoints correctly fetch data and process search queries.
- `OrderTest.php`: Validates that users cannot place an order if they are not logged in.

**Fixing the Database for Tests**  
By default, tests use a fresh, in-memory SQLite database. I fixed several duplication issues in the migration files by adding safety checks like `if (!Schema::hasColumn(...))`. This allows the testing database to build successfully without column-already-exists errors.

**How to run the tests:**
1. Open your terminal and go to the backend folder: `cd backend`
2. Run the test command: `php artisan test`

## Frontend Testing (Next.js)

I set up **Jest** and **React Testing Library** to verify your frontend components and logic.

The following tests were created:
- `Setup.test.tsx`: A baseline test to confirm the environment is correctly configured.
- `CartContext.test.tsx`: Tests the shopping cart logic, including adding items, updating quantities, and clearing the cart (simulating LocalStorage).
- `ProductDetailModal.test.tsx`: Verified that the product modal correctly renders data, calculates subtotals, and triggers the "Add to Cart" action.

**How to run the tests:**
1. Open your terminal and go to the frontend folder: `cd frontend`
2. Run the test command: `npm run test`
