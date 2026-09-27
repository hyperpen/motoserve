import { Component, OnInit, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { RouterLink } from '@angular/router';
import {
  IonButton,
  IonContent,
  IonHeader,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import { Customer } from '../services/auth.service';
import { CustomerApiService } from '../services/customer-api.service';

@Component({
  selector: 'app-tab1',
  standalone: true,
  imports: [
    IonButton,
    IonContent,
    IonHeader,
    IonText,
    IonTitle,
    IonToolbar,
    RouterLink,
  ],
  template: `
    <ion-header><ion-toolbar><ion-title>MotoServe</ion-title></ion-toolbar></ion-header>
    <ion-content><main class="ms-shell ms-shell--narrow">
      <div class="home-top"><span class="home-logo">M</span><span>MotoServe <small>CUSTOMER</small></span></div>
      @if (loading()) { <p class="ms-muted">Loading your account...</p> }
      @else if (errorMessage()) { <ion-text color="danger"><p>{{ errorMessage() }}</p></ion-text><ion-button size="small" (click)="loadProfile()">Retry</ion-button> }
      @else if (customer(); as user) {
        <div class="welcome-card"><p>WELCOME BACK</p><h1>Hi, {{ user.name.split(' ')[0] }} 👋</h1><span>Everything your motorcycle needs, all in one place.</span><ion-button size="small" color="light" href="/tabs/tab3">Book a Service</ion-button></div>
      }
      <h2 class="ms-section-title">What would you like to do?</h2>
      <div class="quick-grid">
        <a routerLink="/tabs/tab2"><span>🏍️</span><strong>My Motorcycles</strong><small>Manage your rides</small></a>
        <a routerLink="/tabs/tab3"><span>🗓️</span><strong>Appointments</strong><small>Book and track</small></a>
        <a routerLink="/tabs/tab4"><span>🛠️</span><strong>Parts Shop</strong><small>Browse products</small></a>
        <a routerLink="/orders"><span>📦</span><strong>Pickup Orders</strong><small>View order status</small></a>
      </div>
    </main></ion-content>
  `,
  styles: [`
    .home-top { display: flex; gap: 10px; align-items: center; font-size: 19px; font-weight: 800; margin: 8px 0 22px; }
    .home-top small { display: block; color: #9caac2; font-size: 9px; letter-spacing: 2px; }
    .home-logo { width: 32px; height: 32px; display: grid; place-items: center; color: white; border-radius: 10px; background: linear-gradient(135deg,#668aff,#976cff); }
    .welcome-card { border-radius: 20px; background: linear-gradient(125deg,#274a91,#5d49a4); padding: 24px; }
    .welcome-card p { font-size: 10px; letter-spacing: 2px; margin: 0; }
    .welcome-card h1 { margin: 8px 0; font-size: 27px; }
    .welcome-card span { display: block; font-size: 13px; color: #e4e9ff; max-width: 280px; }
    .welcome-card ion-button { margin-top: 20px; --border-radius: 10px; }
    .quick-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; }
    .quick-grid a { display: flex; flex-direction: column; gap: 5px; padding: 16px; color: inherit; background: #1a2130; border: 1px solid #2c374a; border-radius: 17px; text-decoration: none; }
    .quick-grid a span { font-size: 26px; margin-bottom: 5px; }
    .quick-grid strong { font-size: 13px; }
    .quick-grid small { color: #9caac2; font-size: 11px; }
  `],
})
export class Tab1Page implements OnInit {
  private api = inject(CustomerApiService);

  customer = signal<Customer | null>(null);
  loading = signal(true);
  errorMessage = signal('');

  ngOnInit() {
    void this.loadProfile();
  }

  async loadProfile() {
    this.loading.set(true);
    this.errorMessage.set('');

    try {
      const user = await firstValueFrom(
        this.api.get<Customer>('me')
      );
      this.customer.set(user);
    } catch (error) {
      if (error instanceof HttpErrorResponse) {
        this.errorMessage.set(
          error.status === 401
            ? 'Session expired. Please log in again.'
            : error.error?.message ?? `API error (${error.status}).`
        );
      } else {
        this.errorMessage.set('Cannot load customer account.');
      }
    } finally {
      this.loading.set(false);
    }
  }
}