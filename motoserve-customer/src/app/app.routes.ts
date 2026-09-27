import { Routes } from '@angular/router';
import {
  customerGuard,
  guestGuard,
} from './guards/session.guard';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
  {
    path: 'login',
    canActivate: [guestGuard],
    loadComponent: () =>
      import('./pages/login/login.page')
        .then(m => m.LoginPage),
  },
  {
    path: 'register',
    canActivate: [guestGuard],
    loadComponent: () =>
      import('./pages/register/register.page')
        .then(m => m.RegisterPage),
  },
  {
    path: 'cart',
    canActivate: [customerGuard],
    loadComponent: () =>
      import('./pages/cart/cart.page')
        .then(m => m.CartPage),
  },
  {
    path: 'orders',
    canActivate: [customerGuard],
    loadComponent: () =>
      import('./pages/orders/orders.page')
        .then(m => m.OrdersPage),
  },
  {
    path: '',
    canActivate: [customerGuard],
    loadChildren: () =>
      import('./tabs/tabs.routes')
        .then(m => m.routes),
  },
];