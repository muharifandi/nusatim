package com.nusatim.partner.features.leads.ui.leads.detail

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadActivityResponse
import com.nusatim.partner.features.leads.databinding.ItemLeadActivityBinding

class LeadActivityAdapter : ListAdapter<LeadActivityResponse, LeadActivityAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemLeadActivityBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), position == 0, position == itemCount - 1)
    }

    inner class ViewHolder(private val binding: ItemLeadActivityBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: LeadActivityResponse, isFirst: Boolean, isLast: Boolean) {
            binding.tvBody.text = item.body
            binding.tvDate.text = item.createdAt
            
            binding.viewLineTop.visibility = if (isFirst) View.INVISIBLE else View.VISIBLE
            binding.viewLineBottom.visibility = if (isLast) View.INVISIBLE else View.VISIBLE

            val iconRes = when (item.type) {
                "status_change" -> android.R.drawable.ic_menu_rotate
                "document" -> android.R.drawable.ic_menu_save
                "note" -> android.R.drawable.ic_menu_edit
                else -> android.R.drawable.ic_menu_info_details
            }
            binding.ivTypeIcon.setImageResource(iconRes)
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<LeadActivityResponse>() {
        override fun areItemsTheSame(oldItem: LeadActivityResponse, newItem: LeadActivityResponse): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: LeadActivityResponse, newItem: LeadActivityResponse): Boolean {
            return oldItem == newItem
        }
    }
}
