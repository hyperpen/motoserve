import {
  ChangeDetectorRef,
  Component,
  OnInit,
  inject,
  signal,
} from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonCardHeader,
  IonCardTitle,
  IonContent,
  IonHeader,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import { CustomerApiService } from '../../services/customer-api.service';
import { ConfirmDialogService } from '../../services/confirm-dialog.service';

interface OrderItem {
  id: number;
  quantity: number;
  unit_price: string;
  subtotal: string;
  product: {
    name: string;
  } | null;
}

interface CustomerOrder {
  id: number;
  order_number: string;
  pickup_code: string;
  pickup_date: string;
  pickup_time: string | null;
  total_amount: string;
  status: string;
  payment_method: string;
  items: OrderItem[];
}

@Component({
  selector: 'app-orders',
  standalone: true,
  imports: [
    IonButton,
    IonCard,
    IonCardContent,
    IonCardHeader,
    IonCardTitle,
    IonContent,
    IonHeader,
    IonText,
    IonTitle,
    IonToolbar,
  ],
  template: `
    <ion-header>
      <ion-toolbar>
        <ion-title>My Pickup Orders</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content><main class="ms-shell ms-shell--narrow">
      <div class="ms-intro"><div><h1>Pickup Orders</h1><p>Check your order and pickup code.</p></div></div>
      <ion-button size="small" fill="clear" (click)="goToShop()">
        ← Back to Shop
      </ion-button>

      <ion-button
        size="small"
        fill="outline"
        [disabled]="loading()"
        (click)="loadOrders()">
        Refresh
      </ion-button>

      @if (errorMessage()) {
        <ion-text color="danger">
          <p>{{ errorMessage() }}</p>
        </ion-text>
      }

      @if (successMessage()) {
        <ion-text color="success">
          <p>{{ successMessage() }}</p>
        </ion-text>
      }

      @if (loading()) {
        <p>Loading orders...</p>
      } @else if (orders().length === 0) {
        <p class="ms-empty">You have no pickup orders yet.</p>
      } @else {
        @for (order of orders(); track order.id) {
          <ion-card>
            <ion-card-header>
              <ion-card-title>
                {{ order.order_number }}
              </ion-card-title>
            </ion-card-header>

            <ion-card-content>
              <p>Status: <strong>{{ order.status }}</strong></p>
              <p>Pickup code: <strong>{{ order.pickup_code }}</strong></p>
              <p>Pickup date: {{ order.pickup_date }}</p>

              @if (order.pickup_time) {
                <p>Pickup time: {{ order.pickup_time }}</p>
              }

              <p>Payment: Pay at store</p>
              <p>Total: {{ formatPrice(order.total_amount) }}</p>

              <h3>Items</h3>

              @for (item of order.items; track item.id) {
                <p>
                  {{ item.product?.name ?? 'Product unavailable' }}
                  × {{ item.quantity }}
                  — {{ formatPrice(item.subtotal) }}
                </p>
              }

              @if (
                order.status === 'pending' ||
                order.status === 'confirmed'
              ) {
                <ion-button
                  size="small"
                  color="danger"
                  fill="outline"
                  [disabled]="cancellingId() === order.id"
                  (click)="cancelOrder(order)">
                  {{
                    cancellingId() === order.id
                      ? 'Cancelling...'
                      : 'Cancel Order'
                  }}
                </ion-button>
              }
            </ion-card-content>
          </ion-card>
        }
      }
    </main></ion-content>
  `,
  styles: [`
    ion-card {
      margin: 12px 0;
    }

    ion-card h3 {
      margin-top: 18px;
    }
  `],
})
export class OrdersPage implements OnInit {
  private api = inject(CustomerApiService);
  private confirmDialog = inject(ConfirmDialogService);
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);

  orders = signal<CustomerOrder[]>([]);
  loading = signal(true);
  cancellingId = signal<number | null>(null);
  errorMessage = signal('');
  successMessage = signal('');

  ngOnInit(): void {
    void this.loadOrders();
  }

  async loadOrders(): Promise<void> {
    this.loading.set(true);
    this.errorMessage.set('');

    try {
      const response = await firstValueFrom(
        this.api.get<{ orders: CustomerOrder[] }>('orders')
      );

      this.orders.set(response.orders);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.loading.set(false);
      this.cdr.markForCheck();
    }
  }

  async cancelOrder(order: CustomerOrder): Promise<void> {
    if (this.cancellingId() !== null) return;

    const confirmed = await this.confirmDialog.confirm({
      header: 'Cancel pickup order?',
      message: `Cancel pickup order ${order.order_number}? Reserved items will be returned to stock.`,
      cancelText: 'Keep Order',
      confirmText: 'Cancel Order',
    });

    if (!confirmed) return;

    this.cancellingId.set(order.id);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const response = await firstValueFrom(
        this.api.patch<{ message: string }>(
          `orders/${order.id}/cancel`,
          {}
        )
      );

      this.successMessage.set(response.message);
      await this.loadOrders();
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.cancellingId.set(null);
      this.cdr.markForCheck();
    }
  }

  goToShop(): void {
    void this.router.navigateByUrl('/tabs/tab4');
  }

  formatPrice(amount: string | number): string {
    return Number(amount).toLocaleString('en-PH', {
      style: 'currency',
      currency: 'PHP',
    });
  }

  private describeError(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      if (error.status === 0) {
        return 'Cannot connect to MotoServe API.';
      }

      return error.error?.message ??
        `Could not load orders (${error.status}).`;
    }

    return 'Could not load orders.';
  }
}
