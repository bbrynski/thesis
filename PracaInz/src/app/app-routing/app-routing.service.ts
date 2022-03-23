import { Injectable } from '@angular/core';
import { AccessService } from '../access/access.service';

@Injectable({
  providedIn: 'root'
})
export class AppRoutingService {

  adminPanelView = {
    isExtendList: null,
    isForm: null
  };
  isAdmin: boolean;
  isEmployee: boolean;

  constructor(private accessService: AccessService ) { }

  checkAdminPanelUrl(currentUrl: string) {
    this.accessService.isAdmin.subscribe(data => this.isAdmin = data);
    this.accessService.isEmployee.subscribe(data => this.isEmployee = data);
    // ustalenie na jakim widoku sie znajdujemy - ulatwienie wyswietlenie odpowiedniej tresci
    if (/adminContent/.test(currentUrl) && /formularz/.test(currentUrl)) {
      this.adminPanelView.isExtendList = false;
      this.adminPanelView.isForm = true;
    } else if (!(/adminContent/.test(currentUrl))) {
      this.adminPanelView.isExtendList = false;
      this.adminPanelView.isForm = false;
    } else {
      this.adminPanelView.isExtendList = true;
      this.adminPanelView.isForm = false;
    }

    return this.adminPanelView;
  }
}
