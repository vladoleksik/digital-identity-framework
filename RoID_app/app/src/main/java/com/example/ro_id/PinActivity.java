package com.example.ro_id;

import static java.lang.Integer.parseInt;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;

import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;

public class PinActivity extends AppCompatActivity {

    String att_id, challenge;

    String pin;

    Button btn_confirm, btn_abort;
    EditText field_pin;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pin);
        Window window = this.getWindow();
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.setStatusBarColor(ContextCompat.getColor(this,R.color.white));
        }
        Bundle bundle = getIntent().getExtras();
        att_id = bundle.getString("att_id");
        challenge = bundle.getString("challenge");
        field_pin = findViewById(R.id.editTextNumberPassword);
        btn_confirm = findViewById(R.id.button4);
        btn_confirm.setOnClickListener(v->{
            if(field_pin.getText()!=null)
            {
                pin = String.valueOf(field_pin.getText());
                Intent i = new Intent(PinActivity.this, NFCActivity.class);
                Bundle bundle2 = new Bundle();
                bundle.putString("att_id", att_id);
                bundle.putString("pin", pin);
                bundle.putString("challenge", challenge);
                i.putExtras(bundle);
                startActivity(i);
            }
            else
            {
                finish();
            }
        });
    }
}