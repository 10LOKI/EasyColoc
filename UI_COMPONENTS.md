# EasyColoc UI Components Documentation

## 🎨 Component Architecture

### Directory Structure
```
resources/views/
├── layouts/           # Main layouts
├── components/
│   ├── ui/           # Reusable UI primitives
│   ├── colocation/   # Colocation-specific components
│   └── expense/      # Expense-specific components
├── colocations/      # Colocation pages
├── expenses/         # Expense pages
└── dashboard.blade.php
```

## 📦 Available Components

### UI Components (Reusable)

#### 1. Card Component
```blade
<x-ui.card title="Optional Title" :padding="true">
    Your content here
</x-ui.card>
```

#### 2. Badge Component
```blade
<x-ui.badge variant="success">Active</x-ui.badge>
<x-ui.badge variant="warning">Pending</x-ui.badge>
<x-ui.badge variant="danger">Overdue</x-ui.badge>
<x-ui.badge variant="info">Info</x-ui.badge>
<x-ui.badge variant="primary">Primary</x-ui.badge>
```

#### 3. Alert Component
```blade
<x-ui.alert type="success" :dismissible="true">
    Operation completed successfully!
</x-ui.alert>

<x-ui.alert type="error">
    Something went wrong!
</x-ui.alert>
```

#### 4. Stat Card Component
```blade
<x-ui.stat-card 
    title="Total Users" 
    value="1,234"
    color="indigo"
    :trend="5"
    :icon="'<svg>...</svg>'"
/>
```

#### 5. Empty State Component
```blade
<x-ui.empty-state 
    title="No data yet"
    description="Get started by creating your first item."
    :action="route('items.create')"
    actionText="Create Item"
/>
```

### Feature Components

#### 6. Colocation Card
```blade
<x-colocation.card :colocation="$colocation" />
```

#### 7. Expense Card
```blade
<x-expense.card :expense="$expense" />
```

## 🎯 Usage Examples

### Dashboard with Stats
```blade
<x-app-layout>
    <x-slot name="header">
        <h2>Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <x-ui.stat-card title="Stat 1" value="100" color="indigo" />
                <x-ui.stat-card title="Stat 2" value="200" color="green" />
            </div>

            <!-- Content Card -->
            <x-ui.card title="Recent Activity">
                Content here
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
```

### List with Empty State
```blade
@if($items->count() > 0)
    <div class="space-y-3">
        @foreach($items as $item)
            <!-- Item card -->
        @endforeach
    </div>
@else
    <x-ui.empty-state 
        title="No items"
        description="Start by adding your first item."
        :action="route('items.create')"
        actionText="Add Item"
    />
@endif
```

## 🎨 Design System

### Colors
- **Primary**: Indigo (main actions)
- **Success**: Green (positive states)
- **Warning**: Yellow (caution states)
- **Danger**: Red (destructive actions)
- **Info**: Blue (informational)

### Spacing
- Use Tailwind's spacing scale: `space-y-6`, `gap-4`, etc.
- Container max-width: `max-w-7xl` for main content
- Card padding: `p-6` by default

### Typography
- Headers: `text-xl font-semibold`
- Body: `text-sm text-gray-600`
- Labels: `text-sm font-medium text-gray-700`

## 🚀 Best Practices

1. **Component Composition**: Build complex UIs from simple components
2. **Consistent Spacing**: Use Tailwind's spacing utilities
3. **Responsive Design**: Always use responsive classes (sm:, lg:)
4. **Accessibility**: Include proper ARIA labels and semantic HTML
5. **Reusability**: Extract repeated patterns into components

## 📝 Creating New Components

```bash
# Create a new component file
resources/views/components/your-component.blade.php
```

```blade
@props(['title', 'required' => false])

<div {{ $attributes->merge(['class' => 'base-classes']) }}>
    <h3>{{ $title }}</h3>
    {{ $slot }}
</div>
```

Usage:
```blade
<x-your-component title="Hello" class="extra-classes">
    Content
</x-your-component>
```

## 🎯 Next Steps

1. Create controllers for colocations and expenses
2. Add routes in `routes/web.php`
3. Implement backend logic
4. Add JavaScript interactions (Alpine.js recommended)
5. Add form validation feedback
6. Implement real-time updates

---

**Pro Tips:**
- Keep components small and focused
- Use slots for flexible content
- Leverage Tailwind's utility classes
- Test responsive behavior on all screen sizes
- Add loading states for better UX
