import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

export default function Login() {
	const [username, setUsername] = useState('');
	const [password, setPassword] = useState('');

	const submit = (e) => {
		e.preventDefault();
		// TODO: wire to authentication endpoint (Inertia or fetch)
		console.log('submit', { username, password });
	};

	return (
		<AppLayout title="Login">
			<div className="max-w-md mx-auto">
				<Card>
					<CardHeader>
						<CardTitle>Sign in to your account</CardTitle>
					</CardHeader>

					<form onSubmit={submit}>
						<CardContent className="space-y-4">
							<div>
								<label className="block text-sm font-medium mb-1">Username</label>
								<Input
									type="text"
									value={username}
									onChange={(e) => setUsername(e.target.value)}
									placeholder="your username"
								/>
							</div>

							<div>
								<label className="block text-sm font-medium mb-1">Password</label>
								<Input
									type="password"
									value={password}
									onChange={(e) => setPassword(e.target.value)}
									placeholder="••••••••"
								/>
							</div>
						</CardContent>

						<CardFooter>
							<Button type="submit" className="w-full">Log In</Button>
						</CardFooter>
					</form>
				</Card>
			</div>
		</AppLayout>
	);
}

