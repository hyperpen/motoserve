import {
  ChangeDetectorRef,
  Component,
  inject,
  signal,
} from '@angular/core';
import { FormsModule } from '@angular/forms';
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
  IonInput,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import { CartService } from '../../services/cart.service';
import { CustomerApiService } from '../../services/customer-api.service';

interface PlacedOrder {
  order_number: string;
  pickup_code: string;
  pickup_date: string;
  pickup_time: string | null;
  total_amount: string;
  status: string;
}

interface PlaceOrderResponse {
  message: string;
  order: PlacedOrder;
}

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [
    FormsModule,
    IonButton,
    IonCard,
    IonCardContent,
    IonCardHeader,
    IonCardTitle,
    IonContent,
    IonHeader,
    IonInput,
    IonText,
    IonTitle,
    IonToolbar,
  ],
  template: `
    <ion-header>
      <ion-toolbar>
        <ion-title>My Cart</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content><main class="ms-shell ms-shell--narrow">
      <div class="ms-intro"><div><h1>My Cart</h1><p>Review your parts and choose a pickup time.</p></div></div>
      @if (placedOrder(); as order) {
        <ion-card>
          <ion-card-header>
            <ion-card-title>Pickup Order Placed!</ion-card-title>
          </ion-card-header>

          <ion-card-content>
            <p>Order number: <strong>{{ order.order_number }}</strong></p>
            <p>Pickup code: <strong>{{ order.pickup_code }}</strong></p>
            <p>Pickup date: {{ order.pickup_date }}</p>

            @if (order.pickup_time) {
              <p>Pickup time: {{ order.pickup_time }}</p>
            }

            <p>Status: {{ order.status }}</p>
            <p>Total: {{ formatPrice(order.total_amount) }}</p>
            <p>Payment: Pay at store</p>
          </ion-card-content>
        </ion-card>

        @if (errorMessage()) {
          <ion-text color="danger">
            <p>{{ errorMessage() }}</p>
          </ion-text>
        }

        <ion-button size="small" (click)="goToShop()">
          Back to Shop
        </ion-button>
      } @else {
        <ion-button size="small" fill="clear" (click)="goToShop()">
          ← Back to Shop
        </ion-button>

        @if (errorMessage()) {
          <ion-text color="danger">
            <p>{{ errorMessage() }}</p>
          </ion-text>
        }

        @if (cart.items().length === 0) {
          <p class="ms-empty">Your cart is empty.</p>

          <ion-button size="small" (click)="goToShop()">
            Browse Products
          </ion-button>
        } @else {
          @for (item of cart.items(); track item.product_id) {
            <ion-card>
              <ion-card-header>
                <ion-card-title>{{ item.name }}</ion-card-title>
              </ion-card-header>

              <ion-card-content>
                <p>Unit price: {{ formatPrice(item.price) }}</p>
                <p>Available stock: {{ item.stock }}</p>
                <p>
                  Subtotal:
                  {{ formatPrice(item.price * item.quantity) }}
                </p>

                <div class="quantity-controls">
                  <ion-button
                    size="small"
                    fill="outline"
                    [disabled]="busy() || placing()"
                    (click)="changeQuantity(
                      item.product_id,
                      item.quantity - 1
                    )">
                    −
                  </ion-button>

                  <strong>{{ item.quantity }}</strong>

                  <ion-button
                    size="small"
                    fill="outline"
                    [disabled]="
                      busy() ||
                      placing() ||
                      item.quantity >= item.stock
                    "
                    (click)="changeQuantity(
                      item.product_id,
                      item.quantity + 1
                    )">
                    +
                  </ion-button>
                </div>

                <ion-button
                  size="small"
                  color="danger"
                  fill="clear"
                  [disabled]="busy() || placing()"
                  (click)="remove(item.product_id)">
                  Remove
                </ion-button>
              </ion-card-content>
            </ion-card>
          }

          <div class="total">
            <strong>Total items: {{ cart.count() }}</strong>
            <h2>Estimated total: {{ formatPrice(cart.subtotal()) }}</h2>
          </div>

          <h2>Pickup Details</h2>

          <form (ngSubmit)="placeOrder()">
            <ion-input
              label="Pickup date"
              labelPlacement="stacked"
              fill="outline"
              type="date"
              name="pickupDate"
              [min]="today"
              [(ngModel)]="pickupDate"
              required>
            </ion-input>

            <ion-input
              label="Pickup time (optional)"
              labelPlacement="stacked"
              fill="outline"
              type="time"
              name="pickupTime"
              [(ngModel)]="pickupTime">
            </ion-input>

            <p>Payment method: Pay at store</p>

            <ion-button
              size="small"
              type="submit"
              [disabled]="placing() || busy()">
              {{ placing() ? 'Placing order...' : 'Place Pickup Order' }}
            </ion-button>
          </form>
        }
      }
    </main></ion-content>
  `,
  styles: [`
    ion-input {
      margin: 14px 0;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 12px;
    }

    .total {
      margin: 24px 8px;
      text-align: right;
    }
    ion-card { margin: 12px 0; }
  `],
})
export class CartPage {
  readonly cart = inject(CartService);

  private api = inject(CustomerApiService);
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);

  busy = signal(false);
  placing = signal(false);
  errorMessage = signal('');
  placedOrder = signal<PlacedOrder | null>(null);

  today = this.localToday();
  pickupDate = this.today;
  pickupTime = '';

  async changeQuantity(
    productId: number,
    quantity: number
  ): Promise<void> {
    this.busy.set(true);
    this.errorMessage.set('');

    try {
      await this.cart.changeQuantity(productId, quantity);
    } catch (error) {
      this.errorMessage.set(
        error instanceof Error
          ? error.message
          : 'Could not update quantity.'
      );
    } finally {
      this.busy.set(false);
      this.cdr.markForCheck();
    }
  }

  async remove(productId: number): Promise<void> {
    this.busy.set(true);
    this.errorMessage.set('');

    try {
      await this.cart.remove(productId);
    } catch {
      this.errorMessage.set('Could not remove the product.');
    } finally {
      this.busy.set(false);
      this.cdr.markForCheck();
    }
  }

  async placeOrder(): Promise<void> {
    if (this.placing() || this.busy() || this.placedOrder()) {
      return;
    }

    if (this.cart.items().length === 0) {
      this.errorMessage.set('Your cart is empty.');
      return;
    }

    if (!this.pickupDate || this.pickupDate < this.localToday()) {
      this.errorMessage.set('Select today or a future pickup date.');
      return;
    }

    this.placing.set(true);
    this.errorMessage.set('');

    try {
      const response = await firstValueFrom(
        this.api.post<PlaceOrderResponse>('orders', {
          pickup_date: this.pickupDate,
          ...(this.pickupTime
            ? { pickup_time: this.pickupTime }
            : {}),
          items: this.cart.items().map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
          })),
        })
      );

      // Ipakita muna ang success para hindi maulit ang order
      // kung sakaling mag-error ang pag-clear ng local cart.
      this.placedOrder.set(response.order);
      await this.cart.clear();
    } catch (error) {
      this.errorMessage.set(
        this.placedOrder()
          ? 'Order created, but the saved cart could not be cleared.'
          : this.describeError(error)
      );
    } finally {
      this.placing.set(false);
      this.cdr.markForCheck();
    }
  }

  goToShop(): void {
    void this.router.navigateByUrl('/tabs/tab4');
  }

  formatPrice(price: number | string): string {
    return Number(price).toLocaleString('en-PH', {
      style: 'currency',
      currency: 'PHP',
    });
  }

  private localToday(): string {
    const now = new Date();
    const local = new Date(
      now.getTime() - now.getTimezoneOffset() * 60_000
    );

    return local.toISOString().slice(0, 10);
  }

  private describeError(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      const errors = error.error?.errors as
        Record<string, string[]> | undefined;

      return (
        (errors ? Object.values(errors)[0]?.[0] : undefined) ??
        error.error?.message ??
        (error.status === 0
          ? 'Cannot connect to MotoServe API.'
          : `Could not place order (${error.status}).`)
      );
    }

    return 'Could not place the order.';
  }
}
