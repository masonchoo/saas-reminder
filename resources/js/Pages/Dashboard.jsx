import { useRef, useState } from "react";
import AppLayout from "@/Layouts/AppLayout";
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useForm, usePage } from '@inertiajs/react';

export default function Dashboard({ subscriptions}) {
    const fileRef = useRef(null);
    const [fileName, setFileName] = useState('');

    // Grab flash messages sent from the Laravel controller
    const { flash } = usePage().props;

    // 1. Initialize the form state
    const { data, setData, post, processing, errors, reset } = useForm({
        file: null,
    });

    function handleSubmit(e) {
        e.preventDefault();

        post('/subscriptions/import', {
            // If the upload is successful, clear the input
            onSuccess: () => {
                reset('file');
                setFileName('');
                if (fileRef.current) fileRef.current.value = '';
            }
        });
        
    }

    function handleFileChange(e) {
        const f = e.target.files?.[0];
        if (f) {
            setFileName(f.name);
            setData('file', f);
        }
    }

    return (
        <AppLayout title="Dashboard">
            <div className="flex gap-6">
                {/* Sidebar */}
                <aside className="w-64">
                    <Card>
                        <CardHeader>
                            <CardTitle>Navigation</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-2">
                            <nav className="flex flex-col gap-2 text-sm">
                                <a href="/dashboard" className="text-foreground">Overview</a>
                                <a href="/upload" className="text-muted-foreground">Upload</a>
                                <a href="/reports" className="text-muted-foreground">Reports</a>
                                <a href="/users" className="text-muted-foreground">Users</a>
                            </nav>
                        </CardContent>
                    </Card>
                </aside>

                {/* Main content */}
                <div className="flex-1 space-y-6">
                    {/* Stats cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>Total Users</CardTitle>
                            </CardHeader>
                            <CardContent className="text-2xl font-bold">1,234</CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Reminders Sent</CardTitle>
                            </CardHeader>
                            <CardContent className="text-2xl font-bold">8,912</CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Pending</CardTitle>
                            </CardHeader>
                            <CardContent className="text-2xl font-bold">27</CardContent>
                        </Card>
                    </div>

                    {/* Upload Panel */}
                    {/* Assuming your bot's username is @MyReminderAgentBot */}
                    
                    <form onSubmit={handleSubmit}>
                        <Card>
                        <CardHeader>
                            <CardTitle>Upload Excel</CardTitle>
                        </CardHeader>
                        <CardContent className="flex items-center justify-between gap-4">
                            <div>
                                <p className="text-sm text-muted-foreground">Upload an Excel file to import subscriptions.</p>
                                {fileName && <p className="mt-1 text-sm">Selected: {fileName}</p>}
                            </div>
                            <div className="flex items-center gap-2">
                                <input ref={fileRef} type="file" accept=".xlsx,.xls" className="hidden" onChange={handleFileChange} />
                                <Button type="button" onClick={() => fileRef.current?.click()}>Choose File</Button>
                                {/* <Button variant="outline">Upload</Button> */}
                                <button 
                                    type="submit"
                                    variant="outline"
                                    className="border border-gray-300 px-4 py-2 rounded disabled:opacity-50"
                                >
                                    Upload
                                </button>
                            </div>
                        </CardContent>
                    </Card>
                    </form>

                    {/* Sample table */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Upcoming Subscription Dues</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-auto">
                                <table className="w-full table-auto">
                                    <thead>
                                        <tr className="text-left text-sm text-muted-foreground">
                                            <th className="px-2 py-2">ID</th>
                                            <th className="px-2 py-2">Subscription Title</th>
                                            <th className="px-2 py-2">Account Name</th>
                                            <th className="px-2 py-2">Next Due Date</th>
                                            <th className="px-2 py-2">Telegram</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {subscriptions.length === 0 ? (
                                            <tr>
                                                <td colSpan="4" className="text-center py-4 text-muted-foreground">
                                                    No subscriptions found. Upload a file to get started.
                                                </td>
                                            </tr>
                                        ) : (
                                            subscriptions.map((row) => (
                                                <tr key={row.id} className="border-t">
                                                    <td className="px-2 py-3 text-sm">{row.id}</td>
                                                    <td className="px-2 py-3 text-sm">{row.subscription_title}</td>
                                                    <td className="px-2 py-3 text-sm">{row.account_name}</td>
                                                    {/* Format the date or provide a fallback if null */}
                                                    <td className="px-2 py-3 text-sm">
                                                        {row.next_due_date ? new Date(row.next_due_date).toLocaleDateString() : '-'}
                                                    </td>
                                                    <td className="px-2 py-3 text-sm">
                                                        <a 
                                                            href={`https://t.me/jupiterrrai_bot?start=sub_${row.id}`} 
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="text-xs bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 inline-block"
                                                        >
                                                            Connect Telegram
                                                        </a>
                                                    </td>
                                                </tr>
                                            ))
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}