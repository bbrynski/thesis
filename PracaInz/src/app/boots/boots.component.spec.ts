import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BootsComponent } from './boots.component';

describe('BootsComponent', () => {
  let component: BootsComponent;
  let fixture: ComponentFixture<BootsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BootsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BootsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
