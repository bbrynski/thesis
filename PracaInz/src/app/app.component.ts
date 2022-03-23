import { Component, OnInit, Inject } from '@angular/core';
import { AccessService } from './access/access.service';
import { LOCAL_STORAGE, StorageService } from 'angular-webstorage-service';
import { JwtHelperService } from '@auth0/angular-jwt';
import { Router } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'app';

  isAdmin = false;
  isEmployee = false;
  isOwner = false;
  // jwt = new JwtHelperService();
  // test: boolean;

  constructor(private accessService: AccessService, private router: Router, @Inject(LOCAL_STORAGE) private localStorage: StorageService) { }

  ngOnInit() {
    this.accessService.checkLocalStorage();
    this.accessService.isAdmin.subscribe(data => {
      this.isAdmin = data;
    });
    this.accessService.isEmployee.subscribe(data => {
      this.isEmployee = data;
    });
    this.accessService.isOwner.subscribe(data => {
      this.isOwner = data;
    });
  }

  logout() {
    localStorage.removeItem('currentUser');
    this.accessService.setIsAdmin(false);
    this.accessService.setIsEmployee(false);
    this.accessService.setIsOwner(false);
    this.router.navigateByUrl('/');
  }

  toReservationList() {
    this.router.navigateByUrl('/zarzadzanie/(adminContent:rezerwacja-lista)');
  }
}
