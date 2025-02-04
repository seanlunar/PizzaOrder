import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Table from '@/components/Table.jsx';

const columns =[
   'size',
   'chef',
   'status',
]


export default function All({ auth, pizzas }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    All Pizzas
                </h2>
            }
        >
            <Head title="All Pizzas" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                       <Table items={pizzas} columns={columns} primary="Order Number" action="pizzas.edit" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
