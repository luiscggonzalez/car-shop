import { createFileRoute } from '@tanstack/react-router'

export const Route = createFileRoute('/')({
  component: Home,
})

function Home() {
  return (
    <div>
      <h2 className="text-2xl font-bold">Welcome to Car Shop</h2>
      <p className="mt-4">This is the home page of the Car Shop application.</p>
    </div>
  )
}
