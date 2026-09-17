import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
 
export default function About() {
    return (
        <AppLayout title="About the Project">
            <div className="prose prose-neutral max-w-none">
                <Card>
                    <CardHeader>
                        <CardTitle>Hey there!</CardTitle>
                        <CardDescription>A bit about how this site was built</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <p>
                            This project was built from scratch using the <strong>Laravel 13 + Inertia + React</strong> stack.
                            No pre-made starter kits — just clean code and full understanding of every layer.
                        </p>
 
                        <div className="grid gap-4 md:grid-cols-3">
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-lg">Backend</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <ul className="text-sm space-y-1">
                                        <li>• Laravel 13</li>
                                        <li>• Inertia.js</li>
                                        <li>• PHP 8.2+</li>
                                        <li>• SQLite/MySQL</li>
                                    </ul>
                                </CardContent>
                            </Card>
 
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-lg">Frontend</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <ul className="text-sm space-y-1">
                                        <li>• React 19</li>
                                        <li>• Vite 8</li>
                                        <li>• shadcn/ui</li>
                                        <li>• Tailwind CSS</li>
                                    </ul>
                                </CardContent>
                            </Card>
 
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-lg">Deployment</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <ul className="text-sm space-y-1">
                                        <li>• Shared hosting</li>
                                        <li>• cPanel/Plesk</li>
                                        <li>• FTP/SFTP</li>
                                        <li>• No Node.js on server</li>
                                    </ul>
                                </CardContent>
                            </Card>
                        </div>
 
                        <p className="text-sm text-muted-foreground">
                            Tip: this template can be used as a foundation for any project.
                            Just copy the structure and add your business logic.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}