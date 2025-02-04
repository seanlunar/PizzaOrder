import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import UpdatePizzaOrderForm from './Partials/UpdatePizzaOrderForm.jsx';



export default function Edit({ auth, pizza }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                  Order #{pizza.id}
                </h2>
            }
        >
            <Head title={ 'Order #' + pizza.id} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <UpdatePizzaOrderForm pizza={pizza} className="max-w-xl" ></UpdatePizzaOrderForm>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
