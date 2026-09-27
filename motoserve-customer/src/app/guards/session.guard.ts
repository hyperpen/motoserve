import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';

import { AuthService, Customer } from '../services/auth.service';
import { CartService } from '../services/cart.service';
import { CustomerApiService } from '../services/customer-api.service';

type SessionStatus = 'valid' | 'invalid' | 'unavailable';

async function checkSession(
  auth: AuthService,
  api: CustomerApiService
): Promise<SessionStatus> {
  let token: string | null;

  try {
    token = await auth.getToken();
  } catch {
    return 'invalid';
  }

  if (!token) return 'invalid';

  try {
    const user = await firstValueFrom(api.get<Customer>('me'));

    return user.role === 'customer' && user.status === 'active'
      ? 'valid'
      : 'invalid';
  } catch (error) {
    if (
      error instanceof HttpErrorResponse &&
      (error.status === 401 || error.status === 403)
    ) {
      return 'invalid';
    }

    return 'unavailable';
  }
}

async function clearLocalSession(
  auth: AuthService,
  cart: CartService
): Promise<void> {
  await Promise.allSettled([
    auth.clearToken(),
    cart.clear(),
  ]);
}

export const customerGuard: CanActivateFn = async () => {
  const auth = inject(AuthService);
  const api = inject(CustomerApiService);
  const cart = inject(CartService);
  const router = inject(Router);

  const status = await checkSession(auth, api);

  if (status === 'invalid') {
    await clearLocalSession(auth, cart);
    return router.parseUrl('/login');
  }

  return true;
};

export const guestGuard: CanActivateFn = async () => {
  const auth = inject(AuthService);
  const api = inject(CustomerApiService);
  const cart = inject(CartService);
  const router = inject(Router);

  const status = await checkSession(auth, api);

  if (status === 'valid') {
    return router.parseUrl('/tabs/tab1');
  }

  if (status === 'invalid') {
    await clearLocalSession(auth, cart);
  }

  return true;
};